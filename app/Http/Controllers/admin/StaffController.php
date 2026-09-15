<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminTransaction;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function staffInformation(Request $request)
    {
        $user = User::with(['role', 'information', 'account'])
            ->findOrFail($request->input('id'));

        return view('admin.staff.staff_information', compact('user'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $query = User::with(['role', 'information'])
            ->whereIn('status', ['Active', 'Disabled']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('staff_ref_num', 'like', "%{$search}%")
                    ->orWhereHas('information', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $staff = $query->orderBy('user_id')->get();

        $html = '';

        foreach ($staff as $member) {
            $fullName = $member->information->last_name.', '.$member->information->first_name.' '.($member->information->middle_name ? $member->information->middle_name[0].'.' : '').' '.$member->information->suffix;
            $roleColor = $member->role->role_name === 'Admin' ? 'purple' : 'blue';
            $statusColor = $member->status === 'Active' ? 'green' : 'red';
            $statusIcon = $member->status === 'Active' ? 'ti ti-circle-check' : 'ti ti-circle-x';
            $staffUrl = '/admin/staff/staff_information?id='.$member->user_id;

            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($member->staff_ref_num).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($fullName).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($member->information->contact_number).'</td>';
            $html .= '<td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba('.($roleColor === 'purple' ? '168, 85, 247' : '59, 130, 246').', 0.10); color: '.($roleColor === 'purple' ? '#c084fc' : '#60a5fa').'; border-color: rgba('.($roleColor === 'purple' ? '168, 85, 247' : '59, 130, 246').', 0.30);"><span class="text-[0.7rem]">'.e($member->role->role_name).'</span></span></td>';
            $html .= '<td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba('.($statusColor === 'green' ? '34, 197, 94' : '239, 68, 68').', 0.10); color: '.($statusColor === 'green' ? '#4ade80' : '#f87171').'; border-color: rgba('.($statusColor === 'green' ? '34, 197, 94' : '239, 68, 68').', 0.30);"><i class="'.$statusIcon.' text-[0.7rem]"></i><span class="text-[0.7rem]">'.e($member->status).'</span></span></td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5">';
            $html .= '<a href="'.$staffUrl.'" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Staff"><i class="ti ti-eye text-xs text-white"></i></a>';
            $html .= '<button type="button" data-modal-open="archive-confirmation-modal" data-id="'.$member->user_id.'" data-ref="'.e($member->staff_ref_num).'" data-name="'.e($fullName).'" data-status="'.e($member->status).'" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-red-600 hover:bg-red-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Archive Staff"><i class="ti ti-archive text-xs text-white"></i></button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($staff->isEmpty()) {
            $html = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No staff members found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html, 'count' => $staff->count()]);
    }

    public function searchArchives(Request $request)
    {
        $search = $request->input('search', '');

        $query = User::with(['role', 'information'])
            ->where('status', 'Soft Delete');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('staff_ref_num', 'like', "%{$search}%")
                    ->orWhereHas('information', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $archived = $query->orderBy('user_id')->get();

        $html = '';

        foreach ($archived as $member) {
            $fullName = $member->information->last_name.', '.$member->information->first_name.' '.($member->information->middle_name ? $member->information->middle_name[0].'.' : '').' '.$member->information->suffix;
            $remaining = max(0, 30 - intdiv($member->updated_at->diffInMilliseconds(now()), 86400000));

            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($member->staff_ref_num).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.e($fullName).'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.$member->updated_at->format('m/d/Y').'</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">'.$remaining.' days</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center">';
            $html .= '<button type="button" data-modal-open="unarchive-confirmation-modal" data-id="'.$member->user_id.'" data-ref="'.e($member->staff_ref_num).'" data-name="'.e($fullName).'" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-green-600 hover:bg-green-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Unarchive Staff"><i class="ti ti-archive-off text-xs text-white"></i></button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($archived->isEmpty()) {
            $html = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No archived staff found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html, 'count' => $archived->count()]);
    }

    public function recycleBin()
    {
        $archived = User::with(['role', 'information'])
            ->where('status', 'Soft Delete')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.staff.recycle_bin_staffs', compact('archived'));
    }

    public function validateStep(Request $request)
    {
        $step = $request->input('step');
        $validRoleIds = Role::pluck('role_id')->toArray();

        try {

            if ($step === 'info') {

                $request->validate([

                    'last' => ['required', 'string', 'max:100'],
                    'first' => ['required', 'string', 'max:100'],
                    'middle' => ['nullable', 'string', 'max:100'],
                    'suffix' => ['nullable', 'string', 'max:20'],
                    'birthdate' => ['required', 'date'],
                    'gender' => ['required', 'string', 'in:Male,Female,Other'],
                    'contact' => ['required', 'string', 'max:20'],
                    'barangay' => ['required', 'string', 'max:255'],
                    'city' => ['required', 'string', 'max:255'],
                    'province' => ['required', 'string', 'max:255'],

                ], [

                    'last.required' => 'Last name is required.',
                    'first.required' => 'First name is required.',
                    'birthdate.required' => 'Birthdate is required.',
                    'gender.required' => 'Gender is required.',
                    'gender.in' => 'Invalid gender.',
                    'contact.required' => 'Contact number is required.',
                    'barangay.required' => 'Barangay is required.',

                ]);

            } elseif ($step === 'account') {

                $request->validate([

                    'role_id' => ['required', 'in:'.implode(',', $validRoleIds)],

                    'email' => [
                        'required',
                        'email',
                        'regex:/^[^\s@]+@biringancity\.gov\.ph$/i',
                        'unique:user_accounts,email',
                    ],

                    'password' => [
                        'required',
                        'confirmed',
                        'min:6',
                    ],

                ], [

                    'role_id.required' => 'Position is required.',
                    'role_id.in' => 'Invalid position.',
                    'email.required' => 'Email is required.',
                    'email.regex' => 'Must be a valid @biringancity.gov.ph email.',
                    'email.unique' => 'This email is already taken.',
                    'password.required' => 'Password is required.',
                    'password.confirmed' => 'Passwords do not match.',
                    'password.min' => 'Password must be at least 6 characters.',

                ]);

            }

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Validation passed.',
        ]);
    }

    public function store(Request $request)
    {
        $validRoleIds = Role::pluck('role_id')->toArray();

        try {

            $validated = $request->validate([

                'last' => ['required', 'string', 'max:100'],
                'first' => ['required', 'string', 'max:100'],
                'middle' => ['nullable', 'string', 'max:100'],
                'suffix' => ['nullable', 'string', 'max:20'],
                'birthdate' => ['required', 'date'],
                'gender' => ['required', 'string', 'in:Male,Female,Other'],
                'contact' => ['required', 'string', 'max:20'],
                'barangay' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'province' => ['required', 'string', 'max:255'],

                'role_id' => ['required', 'in:'.implode(',', $validRoleIds)],

                'email' => [
                    'required',
                    'email',
                    'regex:/^[^\s@]+@biringancity\.gov\.ph$/i',
                    'unique:user_accounts,email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    'min:6',
                ],

            ], [

                'last.required' => 'Last name is required.',
                'first.required' => 'First name is required.',
                'birthdate.required' => 'Birthdate is required.',
                'gender.required' => 'Gender is required.',
                'gender.in' => 'Invalid gender.',
                'contact.required' => 'Contact number is required.',
                'barangay.required' => 'Barangay is required.',
                'role_id.required' => 'Position is required.',
                'role_id.in' => 'Invalid position.',
                'email.required' => 'Email is required.',
                'email.regex' => 'Must be a valid @biringancity.gov.ph email.',
                'email.unique' => 'This email is already taken.',
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Passwords do not match.',
                'password.min' => 'Password must be at least 6 characters.',

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

        $result = DB::transaction(function () use ($request) {

            $info = UserInformation::create([

                'first_name' => $request->input('first'),
                'last_name' => $request->input('last'),
                'middle_name' => $request->input('middle'),
                'suffix' => $request->input('suffix'),
                'gender' => $request->input('gender'),
                'birth_date' => $request->input('birthdate'),
                'barangay' => $request->input('barangay'),
                'city' => $request->input('city'),
                'province' => $request->input('province'),
                'contact_number' => $request->input('contact'),

            ]);

            $account = UserAccount::create([

                'email' => strtolower($request->input('email')),
                'password_hash' => Hash::make($request->input('password')),

            ]);

            $refNum = $this->generateRefNum();

            $user = User::create([

                'staff_ref_num' => $refNum,
                'role_id' => $request->input('role_id'),
                'user_info_id' => $info->user_info_id,
                'user_acc_id' => $account->user_acc_id,
                'status' => 'Active',

            ]);

            return $user;
        });

        $this->logTransaction(
            'New Staff Added',
            'User',
            $result->user_id,
            'Added new staff: '.$result->staff_ref_num
        );

        if ($request->expectsJson()) {

            $user = User::with(['role', 'information'])->find($result->user_id);

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully.',
                'staff' => [
                    'user_id' => $user->user_id,
                    'staff_ref_num' => $user->staff_ref_num,
                    'full_name' => $user->information->last_name.', '.$user->information->first_name.' '.($user->information->middle_name ? $user->information->middle_name[0].'.' : '').' '.$user->information->suffix,
                    'role_name' => $user->role->role_name,
                    'contact_number' => $user->information->contact_number,
                    'status' => $user->status,
                    'email' => $user->account->email,
                ],
            ]);
        }

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Staff created successfully.');
    }

    private function generateRefNum()
    {
        do {
            $refNum = '2026'.str_pad(random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (User::where('staff_ref_num', $refNum)->exists());

        return $refNum;
    }

    public function archive(Request $request)
    {
        try {

            $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,user_id'],
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::findOrFail($request->input('user_id'));
        $oldStatus = $user->status;
        $user->update(['status' => 'Soft Delete']);

        $this->logTransaction(
            'Staff Status Updated',
            'User',
            $user->user_id,
            'Deleted staff: '.$user->staff_ref_num,
            ['status' => $oldStatus],
            ['status' => 'Soft Delete']
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Staff deleted successfully.');
    }

    public function unarchive(Request $request)
    {
        try {

            $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,user_id'],
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::findOrFail($request->input('user_id'));
        $oldStatus = $user->status;
        $user->update(['status' => 'Active']);

        $this->logTransaction(
            'Staff Status Updated',
            'User',
            $user->user_id,
            'Restored staff: '.$user->staff_ref_num,
            ['status' => $oldStatus],
            ['status' => 'Active']
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Staff restored successfully.',
            ]);
        }

        return redirect()
            ->route('admin.staff.recycle_bin')
            ->with('success', 'Staff restored successfully.');
    }

    public function updatePassword(Request $request)
    {
        try {

            $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,user_id'],
                'password' => ['required', 'confirmed', 'min:6'],
            ], [
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Passwords do not match.',
                'password.min' => 'Password must be at least 6 characters.',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::findOrFail($request->input('user_id'));
        $user->account->update([
            'password_hash' => Hash::make($request->input('password')),
        ]);

        $this->logTransaction(
            'Staff Password Updated',
            'User',
            $user->user_id,
            'Updated password for staff: '.$user->staff_ref_num
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.',
            ]);
        }

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Password updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        try {

            $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,user_id'],
                'status' => ['required', 'in:Active,Disabled'],
            ], [
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status.',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::findOrFail($request->input('user_id'));
        $oldStatus = $user->status;
        $newStatus = $request->input('status');
        $user->update(['status' => $newStatus]);

        $this->logTransaction(
            'Staff Status Updated',
            'User',
            $user->user_id,
            'Updated status for staff: '.$user->staff_ref_num,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        }

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Status updated successfully.');
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
