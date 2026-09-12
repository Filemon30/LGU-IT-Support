<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminTransaction;
use App\Models\Division;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OfficeController extends Controller
{
    public function officeDivisions(Office $office)
    {
        $divisions = Division::where('office_id', $office->office_id)
            ->orderBy('division_id')
            ->get();

        return view('admin.offices.office_divisions', compact('office', 'divisions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'office_name' => ['required', 'string', 'max:255', 'unique:offices,office_name'],
            ], [
                'office_name.required' => 'Office name is required.',
                'office_name.unique' => 'This office already exists.',
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

            $office = Office::create([
                'office_ref_num' => $refNum,
                'office_name' => $validated['office_name'],
                'status' => 'Active',
            ]);

            return $office;
        });

        $this->logTransaction(
            'New Office Added',
            'Office',
            $result->office_id,
            'Added new office: '.$result->office_name
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Office added successfully.',
                'office' => [
                    'office_id' => $result->office_id,
                    'office_ref_num' => $result->office_ref_num,
                    'office_name' => $result->office_name,
                    'status' => $result->status,
                    'divisions_count' => 0,
                ],
            ]);
        }

        return redirect()
            ->route('admin.offices')
            ->with('success', 'Office added successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');

        $query = Office::withCount('divisions');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('office_ref_num', 'like', "%{$search}%")
                    ->orWhere('office_name', 'like', "%{$search}%");
            });
        }

        $offices = $query->orderBy('office_id')->get();

        $html = '';

        foreach ($offices as $office) {
            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($office->office_ref_num).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($office->office_name).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.$office->divisions_count.'</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5">';
            $html .= '<a href="'.route('admin.offices.office_divisions', $office).'" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Office"><i class="ti ti-eye text-xs text-white"></i></a>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($offices->isEmpty()) {
            $html = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No offices found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html, 'count' => $offices->count()]);
    }

    public function loadOffices()
    {
        $offices = Office::withCount('divisions')->orderBy('office_id')->get();

        $html = '';

        foreach ($offices as $office) {
            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($office->office_ref_num).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($office->office_name).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.$office->divisions_count.'</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5">';
            $html .= '<a href="'.route('admin.offices.office_divisions', $office).'" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Office"><i class="ti ti-eye text-xs text-white"></i></a>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($offices->isEmpty()) {
            $html = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No offices found.</td></tr>';
        }

        $officeCount = $offices->count();
        $divisionCount = $offices->sum('divisions_count');

        return response()->json([
            'success' => true,
            'html' => $html,
            'office_count' => $officeCount,
            'division_count' => $divisionCount,
        ]);
    }

    private function generateRefNum()
    {
        $last = Office::orderByRaw("CAST(SUBSTRING_INDEX(office_ref_num, '-', -1) AS UNSIGNED) DESC")
            ->value('office_ref_num');

        $nextNum = 1;

        if ($last) {
            $nextNum = (int) substr($last, strrpos($last, '-') + 1) + 1;
        }

        return 'OFF-'.str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    private function logTransaction($actionType, $entityType, $entityId, $description = null, $oldValues = null, $newValues = null)
    {
        $refNum = str_pad(random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);

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
