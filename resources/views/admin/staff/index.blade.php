@extends('layouts.app')

@section('title', 'Staff')

@section('content')

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/staff.js') }}"></script>

    <input type="hidden" id="current-user-id" value="{{ session('user_id') }}">

<div class="space-y-6">
            
        <x-card>

            <div class="flex grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-overview_card
                    icon="ti ti-users-group"
                    label="Total Staffs"
                    total="{{ $totalStaff }}"
                    color="blue"
                    id="total-staff"
                />

                <x-overview_card
                    icon="ti ti-user-check"
                    label="Active Accounts"
                    total="{{ $activeStaff }}"
                    color="green"
                    id="active-staff"
                />

                <x-overview_card
                    icon="ti ti-user-cancel"
                    label="Disabled Accounts"
                    total="{{ $deactivatedStaff }}"
                    color="red"
                    id="deactivated-staff"
                />
            </div> 
            
            <div class="border-t border-gray-200 mb-4 mt-4"></div>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:w-auto">

                    {{-- Search --}}
                    <x-input_white
                        name="search"
                        type="text"
                        placeholder="Search by name or id"
                        icon="ti ti-search"
                        class="w-full h-8 sm:w-[20rem]"
                    />

                    {{-- Search Button --}}
                    <x-button
                        color="d-blue"
                        type="button"
                        class="h-9 w-full lg:inline-flex sm:w-auto text-xs"
                    >
                        Search
                    </x-button>
                    

                    {{-- Status --}}
                    <x-dropdown
                        name="status"
                        placeholder="All Statuses"
                        :options="[
                            'active' => 'Active',
                            'disabled' => 'Disabled',
                        ]"
                        class="w-full h-9 sm:ml-4 sm:w-[10rem]"
                    />

                </div>

                <div class="gap-2">

                    <x-button
                        color="outline-green"
                        type="button"
                        icon="ti ti-user-plus"
                        data-modal-open="info-staff-modal"
                    >
                        Add Staff
                    </x-button>

                    <x-button
                        color="outline-red"
                        type="button"
                        icon="ti ti-trash"
                        href="{{ route('admin.staff.recycle_bin')}}"
                    >
                        Recycle Bin
                    </x-button>
                </div>
            </div>

        </x-card>


        {{-- Staff Table --}}
        <x-card>

            <x-table
                :columns="[
                    'ID',
                    'Full Name',
                    'Contact No.',
                    'Position',
                    'Account Status',
                ]"
                :actions="true"
            >

                <x-slot:body>

                    @forelse ($staff as $member)
                        <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                {{ $member->staff_ref_num }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                {{ $member->information->last_name }}, {{ $member->information->first_name }} {{ $member->information->middle_name ? $member->information->middle_name[0] . '.' : '' }} {{ $member->information->suffix }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                {{ $member->information->contact_number }}
                            </td>
                            <td class="px-4 py-3">
                                <x-badge
                                    label="{{ $member->role->role_name }}"
                                    color="{{ $member->role->role_name === 'Admin' ? 'purple' : 'blue' }}"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <x-badge
                                    label="{{ $member->status }}"
                                    color="{{ $member->status === 'Active' ? 'green' : 'red' }}"
                                    icon="{{ $member->status === 'Active' ? 'ti ti-circle-check' : 'ti ti-circle-x' }}"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <x-action_btn
                                        icon="ti ti-eye"
                                        color="blue"
                                        title="View Staff"
                                        href="{{ route('admin.staff.staff_information', ['id' => $member->user_id]) }}"
                                    />

                                    <x-action_btn
                                        icon="ti ti-trash"
                                        color="red"
                                        title="Delete Staff"
                                        data-modal-open="delete-confirmation-modal"
                                        data-id="{{ $member->user_id }}"
                                        data-ref="{{ $member->staff_ref_num }}"
                                        data-name="{{ $member->information->last_name }}, {{ $member->information->first_name }} {{ $member->information->middle_name ? $member->information->middle_name[0] . '.' : '' }} {{ $member->information->suffix }}"
                                        data-status="{{ $member->status }}"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 mt-10">
                                No staff members found.
                            </td>
                        </tr>
                    @endforelse

                </x-slot:body>

            </x-table>

        </x-card>

    </div>

    {{-- Staff Information --}}
    <x-modal_form
        id="info-staff-modal"
        title="New Staff"
        icon="ti ti-user-plus"
    >
        <form>

            <h2 class="text-lg font-semibold text-gray-900">
                Staff Information
            </h2>

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

                {{-- Last Name --}}
                <x-input_white
                    name="last"
                    type="text"
                    placeholder="Last Name"
                    label="Last Name"
                />

                {{-- First Name --}}
                <x-input_white
                    name="first"
                    type="text"
                    placeholder="First Name"
                    label="First Name"
                />

                {{-- Middle Name --}}
                <x-input_white
                    name="middle"
                    type="text"
                    placeholder="Middle Name"
                    label="Middle Name"
                    sublabel="( Optional )"
                />

            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">

                {{-- Birthdate --}}
                <x-date_picker
                    name="birthdate"
                    placeholder="mm/dd/yyyy"
                    label="Birthdate"
                />

                {{-- Suffix --}}
                <x-dropdown
                    name="suffix"
                    placeholder="Select Suffix"
                    size="md"
                    label="Suffix"
                    sublabel="( Optional )"
                />
    
                {{-- Gender --}}
                <x-dropdown
                    name="gender"
                    placeholder="Select Gender"
                    size="md"
                    label="Gender"
                    :options="collect($genderOptions)->mapWithKeys(fn($g) => [$g => $g])->toArray()"
                />
    
                {{-- Contact --}}
                <x-input_white
                    name="contact"
                    type="text"
                    placeholder="09876543210"
                    label="Contact No."
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

                {{-- Barangay --}}
                <x-dropdown
                    name="barangay"
                    placeholder="Select Barangay"
                    size="md"
                    label="Barangay"
                    :options="$barangays->pluck('barangay_name', 'barangay_name')->toArray()"
                />

                {{-- City --}}
                <input type="hidden" name="city" value="Biringan City">

                {{-- Province --}}
                <input type="hidden" name="province" value="Encantadia">

                {{-- Buttons --}}
                <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

                    <x-button
                        type="button"
                        color="outline-red"
                        data-modal-close="info-staff-modal"
                    >
                        Back
                    </x-button>

                    <x-button
                        type="button"
                        color="outline-green"
                        onclick="validateAndProceedInfo()"
                    >
                        Proceed
                    </x-button>

                </div>

            </div>

        </form>

    </x-modal_form>
       

    {{-- Account Information --}}
    <x-modal_form
        id="account-staff-modal"
        title="New Staff"
        icon="ti ti-user-plus"
        width="max-w-sm"
    >
        <form>

            {{-- Section Title --}}
            <h2 class="text-lg font-semibold text-gray-900">
                Create Account
            </h2>

            @csrf

            {{-- Centered Form --}}
            <div class="flex flex-col items-center gap-3 mt-4">

                {{-- Role --}}
                <div class="w-full max-w-50">
                    <x-dropdown
                        name="role_id"
                        placeholder="Select Position"
                        size="md"
                        label="Position"
                        :options="$roles->pluck('role_name', 'role_id')->toArray()"
                    />
                </div>

                {{-- Email --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="email"
                        type="email"
                        placeholder="email@biringancity.gov.ph"
                        label="Email"
                    />
                </div>

                {{-- Password --}}
                <div class="w-full max-w-50">
                    <label class="text-xs font-semibold mb-1 block">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="**********"
                            class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 pr-9 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-9 text-gray-400 hover:text-gray-600"
                        >
                            <i class="ti ti-eye text-base"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="w-full max-w-50">
                    <label class="text-xs font-semibold mb-1 block">Re-enter Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="**********"
                            class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 pr-9 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-9 text-gray-400 hover:text-gray-600"
                        >
                            <i class="ti ti-eye text-base"></i>
                        </button>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex w-full items-center justify-end gap-3 mt-5">

                    <x-button
                        type="button"
                        color="outline-red"
                        data-modal-close="account-staff-modal"
                        data-modal-open="info-staff-modal"
                    >
                        Back
                    </x-button>

                    <x-button
                        type="button"
                        color="outline-green"
                        onclick="validateAndProceedAccount()"
                    >
                        Proceed
                    </x-button>

                </div>
            </div>
        </form>
    </x-modal_form>


    {{--- Staff Confirmation ---}}
    <x-modal_form
        id="confirmation-staff-modal"
        title="New Staff"
        icon="ti ti-user-plus"
    >
        <form>
            <h2 class="text-lg font-semibold text-gray-900">
                Staff Info Confirmation
            </h2>

            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

                {{-- Last Name --}}
                <x-input_white
                    name="last"
                    type="text"
                    placeholder="Last Name"
                    label="Last Name"
                    :editable="false"
                />

                {{-- First Name --}}
                <x-input_white
                    name="first"
                    type="text"
                    placeholder="First Name"
                    label="First Name"
                    :editable="false"
                />

                {{-- Middle Name --}}
                <x-input_white
                    name="middle"
                    type="text"
                    placeholder="Middle Name"
                    label="Middle Name"
                    sublabel="( Optional )"
                    :editable="false"
                />

            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">

                {{-- Birthdate --}}
                <x-input_white
                    name="birthdate"
                    type="text"
                    placeholder="mm/dd/yyyy"
                    label="Birthdate"
                    :editable="false"
                />

                {{-- Suffix --}}
                <x-input_white
                    name="suffix"
                    type="text"
                    placeholder="Select Suffix"
                    label="Suffix"
                    sublabel="( Optional )"
                    :editable="false"
                />
    
                {{-- Gender --}}
                <x-input_white
                    name="gender"
                    type="text"
                    placeholder="Select Gender"
                    label="Gender"
                    :editable="false"
                />
    
                {{-- Contact --}}
                <x-input_white
                    name="contact"
                    type="text"
                    placeholder="09876543210"
                    label="Contact No."
                    :editable="false"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">

                {{-- Barangay --}}
                <x-input_white
                    name="barangay"
                    type="text"
                    placeholder="Select Barangay"
                    label="Barangay"
                    :editable="false"
                />

                {{-- City --}}
                <input type="hidden" name="city" value="Biringan City">
                <x-input_white
                    name="city_display"
                    type="text"
                    value="Biringan City"
                    label="City"
                    sublabel="/ Municipality"
                    :editable="false"
                />

                {{-- Province --}}
                <input type="hidden" name="province" value="Encantadia">
                <x-input_white
                    name="province_display"
                    type="text"
                    value="Encantadia"
                    label="Province"
                    :editable="false"
                />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mt-3">
                Account
            </h2>

            {{-- Centered Form --}}
            <div class="flex flex-col items-center gap-3 mt-3">

                {{-- Role --}}
                <input type="hidden" name="role_id" value="">
                <x-input_white
                    name="role_display"
                    type="text"
                    value=""
                    label="Position"
                    :editable="false"
                />

                {{-- Email --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="email"
                        type="email"
                        placeholder="Email"
                        label="Email"
                        :editable="false"
                    />
                </div>

                {{-- Password --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="password"
                        type="password"
                        placeholder="Password"
                        label="Password"
                        :editable="false"
                    />
                </div>

                <input type="hidden" name="password_confirmation" value="">

                    {{-- Buttons --}}
                <div class="flex w-full items-center justify-end gap-3 mt-5">

                    <x-button
                        type="button"
                        color="outline-red"
                        data-modal-close="confirmation-staff-modal"
                        data-modal-open="account-staff-modal"
                    >
                        Back
                    </x-button>

                    <x-button
                        type="button"
                        color="outline-green"
                        onclick="submitAddStaff()"
                    >
                        Confirm
                    </x-button>

                </div>
            </div>
        </form>
    </x-modal_form>

    {{-- Proceed Loading Modal --}}
    <x-loading_modal id="proceed-loading" text="Proceeding to next step..." />

    {{-- Loading Modal --}}
    <x-loading_modal id="add-staff-loading" text="Adding new staff..." />

    {{-- Success Modal --}}
    <x-success_modal id="add-staff-success" text="New staff account created successfully!" />


    {{--- Delete Confirmation ---}}
    <x-modal_form
        id="delete-confirmation-modal"
        title="Delete Staff"
        icon="ti ti-trash"
        width="max-w-sm"

    >
        <div class="w-fit mx-auto space-y-5">

            <input type="hidden" id="delete-staff-id" value="">

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

                        <span id="delete-staff-ref" class="text-xs font-medium">
                        </span>
                    </div>

                    {{-- Full Name --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Full Name:
                        </span>

                        <span id="delete-staff-name" class="text-xs font-medium">
                        </span>
                    </div>

                    {{-- Account Status --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Account Status:
                        </span>

                        <span id="delete-staff-status" class="text-xs font-medium">
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
@endsection
