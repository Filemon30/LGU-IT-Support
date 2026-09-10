<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminTransaction;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class BarangayController extends Controller
{
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'barangay_name' => ['required', 'string', 'max:255', 'unique:barangays,barangay_name'],
            ], [
                'barangay_name.required' => 'Barangay name is required.',
                'barangay_name.unique' => 'This barangay already exists.',
            ]);

        } catch (ValidationException $e) {

            if ($request->expectsJson()) {

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }

        $result = DB::transaction(function () use ($validated) {

            $refNum = $this->generateRefNum();

            $secretKey = strtoupper(bin2hex(random_bytes(8)));

            $barangay = Barangay::create([
                'barangay_ref_num' => $refNum,
                'barangay_name' => $validated['barangay_name'],
                'secret_key_hash' => $secretKey,
                'key_status' => 'Active',
            ]);

            return $barangay;
        });

        $this->logTransaction(
            'New Barangay Added',
            'Barangay',
            $result->barangay_id,
            'Added new barangay: ' . $result->barangay_name
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Barangay added successfully.',
                'barangay' => [
                    'barangay_id' => $result->barangay_id,
                    'barangay_ref_num' => $result->barangay_ref_num,
                    'barangay_name' => $result->barangay_name,
                    'secret_key' => $result->secret_key_hash,
                    'key_status' => $result->key_status,
                ],
            ]);
        }

        return redirect()
            ->route('admin.barangays')
            ->with('success', 'Barangay added successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = Barangay::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('barangay_ref_num', 'like', "%{$search}%")
                  ->orWhere('barangay_name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('key_status', $status);
        }

        $barangays = $query->orderBy('barangay_id')->get();

        $html = '';

        foreach ($barangays as $barangay) {
            $statusBadge = $barangay->key_status === 'Active'
                ? '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);"><i class="ti ti-circle-check text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($barangay->key_status) . '</span></span>'
                : '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(239, 68, 68, 0.10); color: #f87171; border-color: rgba(239, 68, 68, 0.30);"><i class="ti ti-circle-x text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($barangay->key_status) . '</span></span>';

            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($barangay->barangay_ref_num) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($barangay->barangay_name) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . $statusBadge . '</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5">';
            $html .= '<button type="button" data-modal-open="update-barangay-modal" data-id="' . $barangay->barangay_id . '" data-name="' . e($barangay->barangay_name) . '" data-status="' . e($barangay->key_status) . '" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[#071f45] hover:bg-[#0a2d5e] hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Barangay"><i class="ti ti-refresh text-xs text-white"></i></button>';
            $html .= '<button type="button" data-modal-open="barangay-information-modal" data-ref="' . e($barangay->barangay_ref_num) . '" data-name="' . e($barangay->barangay_name) . '" data-key="' . e($barangay->secret_key_hash) . '" data-status="' . e($barangay->key_status) . '" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Barangay"><i class="ti ti-eye text-xs text-white"></i></button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($barangays->isEmpty()) {
            $html = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No barangays found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html, 'count' => $barangays->count()]);
    }

    private function generateRefNum()
    {
        $last = Barangay::orderByRaw("CAST(SUBSTRING_INDEX(barangay_ref_num, '-', -1) AS UNSIGNED) DESC")
            ->value('barangay_ref_num');

        $nextNum = 1;

        if ($last) {
            $nextNum = (int) substr($last, strrpos($last, '-') + 1) + 1;
        }

        return 'BRGY-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    public function update(Request $request)
    {
        try {

            $request->validate([
                'barangay_id' => ['required', 'integer', 'exists:barangays,barangay_id'],
                'barangay_name' => ['required', 'string', 'max:255', 'unique:barangays,barangay_name,' . $request->input('barangay_id') . ',barangay_id'],
                'key_status' => ['required', 'in:Active,Disabled'],
            ], [
                'barangay_name.required' => 'Barangay name is required.',
                'barangay_name.unique' => 'This barangay name already exists.',
                'key_status.required' => 'Status is required.',
                'key_status.in' => 'Invalid status.',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $barangay = Barangay::findOrFail($request->input('barangay_id'));
        $oldName = $barangay->barangay_name;
        $oldStatus = $barangay->key_status;

        $barangay->update([
            'barangay_name' => $request->input('barangay_name'),
            'key_status' => $request->input('key_status'),
        ]);

        $this->logTransaction(
            $barangay->key_status === 'Active' ? 'Activated Barangay Secret Key' : 'Disabled Barangay Secret Key',
            'Barangay',
            $barangay->barangay_id,
            'Updated barangay: ' . $barangay->barangay_name,
            ['barangay_name' => $oldName, 'key_status' => $oldStatus],
            ['barangay_name' => $barangay->barangay_name, 'key_status' => $barangay->key_status]
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Barangay updated successfully.',
                'barangay' => [
                    'barangay_id' => $barangay->barangay_id,
                    'barangay_ref_num' => $barangay->barangay_ref_num,
                    'barangay_name' => $barangay->barangay_name,
                    'key_status' => $barangay->key_status,
                ],
            ]);
        }

        return redirect()
            ->route('admin.barangays')
            ->with('success', 'Barangay updated successfully.');
    }

    private function logTransaction($actionType, $entityType, $entityId, $description = null, $oldValues = null, $newValues = null)
    {
        $refNum = 'ATRN-' . str_pad(random_int(1, 99999999), 8, '0', STR_PAD_LEFT);

        AdminTransaction::create([
            'admin_transaction_ref' => $refNum,
            'handled_by' => session('user_id'),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}