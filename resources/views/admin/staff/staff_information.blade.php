@extends('layouts.app')

@section('title', 'Staff Information')

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/staff.js') }}"></script>

    <input type="hidden" id="current-user-id" value="{{ session('user_id') }}">
    <input type="hidden" id="delete-staff-id" value="{{ $user->user_id }}">
    <input type="hidden" id="delete-staff-ref" value="{{ $user->staff_ref_num }}">
    <input type="hidden" id="delete-staff-name" value="{{ $user->information->last_name }}, {{ $user->information->first_name }} {{ $user->information->middle_name ? $user->information->middle_name[0] . '.' : '' }} {{ $user->information->suffix }}">
    <input type="hidden" id="delete-staff-status" value="{{ $user->status }}">


    <div class="space-y-6">

        {{-- Staff Information --}}
        <x-card>

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                <x-button
                    color="outline-dark"
                    iconPosition="center"
                    icon="ti ti-arrow-narrow-left"
                    href="{{ route('admin.staff') }}"
                    class="w-fit text-xs"
                >
                    Return
                </x-button>

                

                <div class="inline-flex justify-end h-full">
                    <h1 class="text-xs text-center font-semibold">
                        Account Status:
                    </h1>

                    <x-badge
                        color="{{ $user->status === 'Active' ? 'green' : 'red' }}"
                        class="text-center ml-2 inline-flex"
                    >
                        {{ $user->status }}
                    </x-badge>
                    
                </div>
            </div>


            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                {{-- ID and Date Registered --}}
                <div class="flex flex-col md:flex-row md:items-end gap-3 mt-5">


                    {{-- ID No. --}}
                    <x-input_white
                        name="id"
                        type="text"
                        label="ID No."
                        value="{{ $user->staff_ref_num }}"
                        :editable="false"
                        class="w-fit"
                    />

                    {{-- Position --}}
                    <x-input_white
                        name="Position"
                        type="text"
                        label="Position"
                        value="{{ $user->role->role_name }}"
                        :editable="false"
                        class="w-fit"
                    />

                    {{-- Date Registered --}}
                    <x-input_white
                        name="registered"
                        type="text"
                        label="Date Registered"
                        value="{{ $user->created_at->format('m/d/Y') }}"
                        :editable="false"
                        class="w-fit"
                    />

                    <x-input_white
                    name="username"
                    type="text"
                    value="{{ $user->account->email }}"
                    label="Username"
                    :editable="false"
                    class="w-fit"
                    />

                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2">
                    <x-button
                        color="outline-green"
                        icon="ti ti-edit"
                        iconPosition="left"
                        type="button"
                        onclick="if(document.getElementById('current-user-id').value === '{{ $user->user_id }}'){var m=document.getElementById('self-update-modal');m.classList.remove('hidden');m.classList.add('flex');document.body.classList.add('overflow-hidden');}else{var m=document.getElementById('update-staff-modal');m.classList.remove('hidden');m.classList.add('flex');document.body.classList.add('overflow-hidden');}"
                    >
                        Update
                    </x-button>

                    <x-button
                        color="outline-red"
                        icon="ti ti-trash"
                        iconPosition="left"
                        type="button"
                        onclick="if(document.getElementById('current-user-id').value === '{{ $user->user_id }}'){var m=document.getElementById('self-delete-modal');m.classList.remove('hidden');m.classList.add('flex');document.body.classList.add('overflow-hidden');}else{var m=document.getElementById('delete-confirmation-modal');m.classList.remove('hidden');m.classList.add('flex');document.body.classList.add('overflow-hidden');}"
                    >
                        Delete
                    </x-button>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

                <x-input_white
                    name="last"
                    type="text"
                    value="{{ $user->information->last_name }}"
                    label="Last Name"
                    :editable="false"
                />

                <x-input_white
                    name="first"
                    type="text"
                    value="{{ $user->information->first_name }}"
                    label="First Name"
                    :editable="false"
                />

                <x-input_white
                    name="middle"
                    type="text"
                    value="{{ $user->information->middle_name }}"
                    label="Middle Name"
                    :editable="false"
                />

                <x-input_white
                    name="suffix"
                    type="text"
                    value="{{ $user->information->suffix }}"
                    label="Suffix"
                    :editable="false"
                    class="w-20"
                />

                <x-input_white
                    name="birthdate"
                    type="text"
                    value="{{ $user->information->birth_date ? \Carbon\Carbon::parse($user->information->birth_date)->format('m/d/Y') : '' }}"
                    label="Birthdate"
                    :editable="false"
                    class="w-30"
                />

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                <x-input_white
                    name="gender"
                    type="text"
                    value="{{ $user->information->gender }}"
                    label="Gender"
                    :editable="false"
                    class="w-30"
                />

                <x-input_white
                    name="contact"
                    type="text"
                    value="{{ $user->information->contact_number }}"
                    label="Contact No."
                    :editable="false"
                    class="w-35"
                />

                <x-input_white
                    name="address"
                    type="text"
                    value="{{ $user->information->barangay }}, {{ $user->information->city }}, {{ $user->information->province }}"
                    label="Address"
                    :editable="false"
                    class="w-full"
                />

                

            </div>

        </x-card>


        {{-- Ticket Assigned --}}
        <x-card label="Ticket Assigned">
            <x-table
                :columns="[
                    'Ticket No.',
                    'Requester',
                    'Category',
                    'Status',
                    'Date Archived',
                ]"
                :actions="true"
            >

                <x-slot:body>

                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            TCKT-00001
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            Barangay Amihan
                        </td>

                        <td class="px-4 py-3">
                            <x-badge
                                label="Software"
                                color="blue"
                                icon="ti ti-devices-2"
                            />
                        </td>

                        <td class="px-4 py-3">
                            <x-badge
                                label="Pending"
                                color="orange"
                            />
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            08/29/2026
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-eye"
                                    color="blue"
                                    title="View Ticket"
                                    data-modal-open="ticket-information-modal"
                                />
                            </div>
                        </td>
                    </tr>


                </x-slot:body>

            </x-table>
        </x-card>

    </div>

<x-modal_form
    id="ticket-information-modal"
    title="Ticket Information"
    icon="ti ti-ticket"
>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full items-stretch">

        {{-- Ticket Details --}}
        <x-card
            color="dark"
            class="w-full h-full"
        >

            <div class="grid grid-cols-2 gap-x-4 gap-y-2 items-center text-left">

                <span class="text-xs font-semibold whitespace-nowrap">
                    Ticket No.:
                </span>

                <span class="text-xs">
                    TCKT-00001
                </span>

                <span class="text-xs font-semibold whitespace-nowrap">
                    Category:
                </span>

                <x-badge
                    color="blue"
                    icon="ti ti-devices-2"
                    class="inline-flex items-center justify-center text-center"
                    label="Software"
                />

                <span class="text-xs font-semibold whitespace-nowrap">
                    Date Submitted:
                </span>

                <span class="text-xs">
                    08/30/2026
                </span>

                <span class="text-xs font-semibold whitespace-nowrap">
                    Requester:
                </span>

                <span class="text-xs">
                    Barangay Amihan
                </span>

                <span class="text-xs font-semibold whitespace-nowrap">
                    Priority Level:
                </span>

                <x-badge
                    color="red"
                    class="inline-flex items-center justify-center text-center"
                    label="Critical"
                />

                <span class="text-xs font-semibold whitespace-nowrap">
                    Ticket Status:
                </span>

                <x-badge
                    color="orange"
                    class="inline-flex items-center justify-center text-center"
                    label="Pending"
                />
                

            </div>

        </x-card>


        {{-- Description & Reason --}}
        <x-card
            color="dark"
            class="w-full h-full"
        >

            <div>

                {{-- Reason --}}
                <div>
                    <span class="text-xs font-semibold">
                        Reason:
                    </span>

                    <p class="text-xs text-gray-700">
                        Blue Screen
                    </p>
                </div>

                {{-- Description --}}
                <div>
                    <span class="text-xs font-semibold">
                        Description:
                    </span>

                    <p class="text-xs text-gray-700">
                        Dili ko makatrabaho kay nag blue screen akong pc and naa koy need nga ipasa.
                    </p>
                </div>

                

            </div>

        </x-card>

    </div>

</x-modal_form>

{{--- Delete Confirmation ---}}
    <x-modal_form
        id="delete-confirmation-modal"
        title="Delete Staff"
        icon="ti ti-trash"
        width="max-w-sm"
    >
        <div class="w-fit mx-auto space-y-5">

            {{-- Confirmation Message --}}
            <p class="text-sm text-gray-600 text-center">
                Are you sure you want to delete this staff?
            </p>

            {{-- Staff Information --}}
            <x-card color="red" class="w-full">

                <div class="space-y-2">

                    {{-- ID No --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            ID No:
                        </span>

                        <span class="text-xs font-medium">
                            {{ $user->staff_ref_num }}
                        </span>
                    </div>

                    {{-- Full Name --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Full Name:
                        </span>

                        <span class="text-xs font-medium">
                            {{ $user->information->last_name }}, {{ $user->information->first_name }} {{ $user->information->middle_name ? $user->information->middle_name[0] . '.' : '' }} {{ $user->information->suffix }}
                        </span>
                    </div>

                    {{-- Account Status --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Account Status:
                        </span>

                        <span class="text-xs font-medium">
                            {{ $user->status }}
                        </span>
                    </div>

                </div>

            </x-card>

            {{-- Buttons --}}
            <div class="flex justify-center gap-2">

                <x-button
                    type="button"
                    color="outline-gray"
                    data-modal-close="delete-confirmation-modal"
                >
                   No, Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-red"
                    onclick="confirmDeleteStaff()"
                >
                    Yes, Delete
                </x-button>

            </div>

        </div>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="delete-staff-loading" text="Deleting staff..." />

    {{-- Success Modal --}}
    <x-success_modal id="delete-staff-success" text="Staff deleted successfully!" />

    {{-- Update Loading Modal --}}
    <x-loading_modal id="update-staff-loading" text="Updating staff..." />

    {{-- Update Success Modal --}}
    <x-success_modal id="update-staff-success" text="Staff updated successfully!" />

    <x-modal_form
        id="update-staff-modal"
        title="Update Staff"
        icon="ti ti-user"
        width="max-w-md"
    >
        <form onsubmit="return false;">

            <input type="hidden" name="user_id" value="{{ $user->user_id }}">

            <div class="mb-3">
                <x-dropdown
                    name="update_type"
                    placeholder="Select Update Type"
                    size="md"
                    label="Update Type"
                    :options="[
                        'password' => 'Change Password',
                        'status' => 'Change Account Status',
                    ]"
                />
            </div>

            {{-- Password Fields --}}
            <div id="password-fields" class="hidden space-y-3">
                <div>
                    <label class="text-xs font-semibold mb-1 block">New Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="update-password"
                            placeholder="**********"
                            class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 pr-9 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('update-password', this)"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-9 text-gray-400 hover:text-gray-600"
                        >
                            <i class="ti ti-eye text-base"></i>
                        </button>
                    </div>
                    <p id="password-error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <label class="text-xs font-semibold mb-1 block">Re-enter Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="update-password-confirmation"
                            placeholder="**********"
                            class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 pr-9 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('update-password-confirmation', this)"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-9 text-gray-400 hover:text-gray-600"
                        >
                            <i class="ti ti-eye text-base"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Status Fields --}}
            <div id="status-fields" class="hidden">
                <x-dropdown
                    name="status"
                    placeholder="Select Status"
                    size="md"
                    label="Account Status"
                    :options="[
                        'Active' => 'Active',
                        'Disabled' => 'Disabled',
                    ]"
                />
                <p id="status-error" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div class="flex w-full items-center justify-end gap-3 mt-4">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="update-staff-modal"
                >
                    Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="submitUpdateStaff()"
                >
                    Update
                </x-button>
            </div>

        </form>
    </x-modal_form>

    {{-- Self Delete Prevention Modal --}}
    <div
        id="self-delete-modal"
        data-modal
        data-modal-static
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50">
                <i class="ti ti-alert-triangle text-4xl text-red-500"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">Cannot Delete Account</h3>
            <p class="text-sm text-gray-500 text-center">You cannot delete your own account.</p>
            <x-button
                type="button"
                color="gray"
                data-modal-close="self-delete-modal"
                class="w-full mt-5"
            >
                Okay
            </x-button>
        </div>
    </div>

    {{-- Self Update Prevention Modal --}}
    <div
        id="self-update-modal"
        data-modal
        data-modal-static
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50">
                <i class="ti ti-alert-triangle text-4xl text-red-500"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">Cannot Update Account</h3>
            <p class="text-sm text-gray-500 text-center">You cannot update your own account.</p>
            <x-button
                type="button"
                color="gray"
                data-modal-close="self-update-modal"
                class="w-full mt-5"
            >
                Okay
            </x-button>
        </div>
    </div>


@endsection