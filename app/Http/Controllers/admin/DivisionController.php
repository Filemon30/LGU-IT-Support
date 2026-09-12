<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminTransaction;
use App\Models\Division;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DivisionController extends Controller
{
    public function store(Request $request, Office $office)
    {
        try {
            $validated = $request->validate([
                'division_names' => ['required', 'array', 'min:1', 'max:5'],
                'division_names.*' => ['required', 'string', 'max:255'],
            ], [
                'division_names.required' => 'At least one division name is required.',
                'division_names.array' => 'Invalid division data.',
                'division_names.min' => 'At least one division name is required.',
                'division_names.max' => 'You can add up to 5 divisions at a time.',
                'division_names.*.required' => 'Division name is required.',
                'division_names.*.string' => 'Division name must be a string.',
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

        $divisionNames = array_map('trim', $validated['division_names']);
        $divisionNames = array_filter($divisionNames);
        $divisionNames = array_values($divisionNames);

        if (empty($divisionNames)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => ['division_names' => ['At least one division name is required.']],
            ], 422);
        }

        $existingDivisions = Division::where('office_id', $office->office_id)
            ->whereIn('division_name', $divisionNames)
            ->pluck('division_name')
            ->toArray();

        if (! empty($existingDivisions)) {
            $errors = [];
            foreach ($divisionNames as $index => $name) {
                if (in_array($name, $existingDivisions)) {
                    $errors["division_names.{$index}"] = ["The division \"{$name}\" already exists in this office."];
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        $duplicates = array_diff_key($divisionNames, array_unique($divisionNames));
        if (! empty($duplicates)) {
            $errors = [];
            $seen = [];
            foreach ($divisionNames as $index => $name) {
                if (in_array($name, $seen)) {
                    $errors["division_names.{$index}"] = ["Duplicate division name \"{$name}\"."];
                }
                $seen[] = $name;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        $result = DB::transaction(function () use ($divisionNames, $office) {
            $divisions = [];
            foreach ($divisionNames as $name) {
                $refNum = $this->generateDivisionRefNum();
                $secretKey = strtoupper(bin2hex(random_bytes(8)));

                $division = Division::create([
                    'office_id' => $office->office_id,
                    'division_ref_num' => $refNum,
                    'division_name' => $name,
                    'secret_key_hash' => $secretKey,
                    'key_status' => 'Active',
                    'status' => 'Active',
                ]);

                $divisions[] = $division;
            }

            return $divisions;
        });

        foreach ($result as $division) {
            $this->logTransaction(
                'New Division Added',
                'Division',
                $division->division_id,
                'Added new division: '.$division->division_name.' to office: '.$office->office_name
            );
        }

        if ($request->expectsJson()) {
            $divisionsData = array_map(function ($division) {
                return [
                    'division_id' => $division->division_id,
                    'division_ref_num' => $division->division_ref_num,
                    'division_name' => $division->division_name,
                    'secret_key' => $division->secret_key_hash,
                    'key_status' => $division->key_status,
                ];
            }, $result);

            return response()->json([
                'success' => true,
                'message' => 'Divisions added successfully.',
                'divisions' => $divisionsData,
            ]);
        }

        return redirect()
            ->route('admin.offices.office_divisions', $office)
            ->with('success', 'Divisions added successfully.');
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'division_id' => ['required', 'integer', 'exists:divisions,division_id'],
                'key_status' => ['required', 'in:Active,Disabled'],
            ], [
                'division_id.required' => 'Division is required.',
                'division_id.integer' => 'Invalid division.',
                'division_id.exists' => 'Division not found.',
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

        $division = Division::findOrFail($request->input('division_id'));
        $oldStatus = $division->key_status;

        $division->update([
            'key_status' => $request->input('key_status'),
        ]);

        $this->logTransaction(
            $division->key_status === 'Active' ? 'Activated Division Secret Key' : 'Disabled Division Secret Key',
            'Division',
            $division->division_id,
            'Updated division: '.$division->division_name,
            ['key_status' => $oldStatus],
            ['key_status' => $division->key_status]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Division updated successfully.',
                'division' => [
                    'division_id' => $division->division_id,
                    'division_ref_num' => $division->division_ref_num,
                    'division_name' => $division->division_name,
                    'key_status' => $division->key_status,
                ],
            ]);
        }

        return redirect()
            ->route('admin.offices.office_divisions', $division->office)
            ->with('success', 'Division updated successfully.');
    }

    private function generateDivisionRefNum()
    {
        $last = Division::orderByRaw("CAST(SUBSTRING_INDEX(division_ref_num, '-', -1) AS UNSIGNED) DESC")
            ->value('division_ref_num');

        $nextNum = 1;

        if ($last) {
            $nextNum = (int) substr($last, strrpos($last, '-') + 1) + 1;
        }

        return 'DIV-'.str_pad($nextNum, 5, '0', STR_PAD_LEFT);
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
