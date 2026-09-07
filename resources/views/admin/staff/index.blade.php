@extends('layouts.app')

@section('title', 'Staff')

@section('content')

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/staff.js') }}"></script>

<div class="space-y-6">
            
        <x-card>

            <div class="flex grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-overview_card
                    icon="ti ti-users-group"
                    label="Total Staffs"
                    total="5"
                    color="blue"
                />

                <x-overview_card
                    icon="ti ti-user-check"
                    label="Active Accounts"
                    total="2"
                    color="green"
                />

                <x-overview_card
                    icon="ti ti-user-cancel"
                    label="Deactivated Accounts"
                    total="3"
                    color="red"
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
                            'de-activated' => 'De-activated',
                        ]"
                        class="w-full h-9 sm:ml-4 sm:w-[10rem]"
                    />

                </div>

                <div class="gap-2">

                    <x-button
                        color="d-blue"
                        type="button"
                        icon="ti ti-user-plus"
                        data-modal-open="info-staff-modal"
                    >
                        Add Staff
                    </x-button>

                    <x-button
                        color="outline-red"
                        type="button"
                        icon="ti ti-archive"
                        href="{{ route('admin.staff.staff_archives')}}"
                    >
                        Archives
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
                    'Account Status',
                ]"
                :actions="true"
            >

                <x-slot:body>

                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            IT-00001
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            Galanida, Filemon Jr., L.
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            09123456789
                        </td>
                        <td class="px-4 py-3">
                            <x-badge
                                label="Active"
                                color="green"
                                icon="ti ti-circle-check"
                            />
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-eye"
                                    color="blue"
                                    title="View Staff"
                                    href="{{ route('admin.staff.staff_information')}}"
                                />

                                <x-action_btn
                                    icon="ti ti-archive"
                                    color="red"
                                    title="Achive Staff"
                                    data-modal-open="archive-confirmation-modal"
                                />

                                
                            </div>
                        </td>
                    </tr>


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
        <form action="{{ route('admin.staff') }}">

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
                />

                {{-- City --}}
                <x-input_white
                    name="city"
                    type="text"
                    placeholder="Biringan City"
                    label="City"
                    sublabel="/ Municipality"
                    :editable="false"
                />

                {{-- Province --}}
                <x-input_white
                    name="province"
                    type="text"
                    placeholder="Encantadia"
                    label="Province"
                    :editable="false"
                />

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
                        data-modal-close="info-staff-modal"
                        data-modal-open="account-staff-modal"
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
        <form action="{{ route('admin.staff') }}">

            {{-- Section Title --}}
            <h2 class="text-lg font-semibold text-gray-900">
                Create Account
            </h2>

            @csrf

            {{-- Centered Form --}}
            <div class="flex flex-col items-center gap-3 mt-4">

                {{-- Username --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="username"
                        type="text"
                        placeholder="Username"
                        label="Username"
                    />
                </div>

                {{-- Password --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="password"
                        type="password"
                        placeholder="**********"
                        label="Password"
                    />
                </div>

                {{-- Confirm Password --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="password_confirmation"
                        type="password"
                        placeholder="********"
                        label="Re-enter Password"
                    />
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
                        data-modal-close="account-staff-modal"
                        data-modal-open="confirmation-staff-modal"
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
        <form action="{{ route('admin.staff') }}" method="POST">
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
                <x-input_white
                    name="city"
                    type="text"
                    placeholder="Biringan City"
                    label="City"
                    sublabel="/ Municipality"
                    :editable="false"
                />

                {{-- Province --}}
                <x-input_white
                    name="province"
                    type="text"
                    placeholder="Encantadia"
                    label="Province"
                    :editable="false"
                />
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mt-3">
                Account
            </h2>

            {{-- Centered Form --}}
            <div class="flex flex-col items-center gap-3 mt-3">

                {{-- Username --}}
                <div class="w-full max-w-50">
                    <x-input_white
                        name="username"
                        type="text"
                        placeholder="Username"
                        label="Username"
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
                        onclick="confirmAddStaff()"
                    >
                        Confirm
                    </x-button>

                </div>
            </div>
        </form>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="add-staff-loading" text="Adding new staff..." />

    {{-- Success Modal --}}
    <x-success_modal id="add-staff-success" text="New staff created successfully!" />


    {{--- Archive Confirmation ---}}
    <x-modal_form
        id="archive-confirmation-modal"
        title="Archive Staff"
        icon="ti ti-archive"
        width="max-w-sm"

    >
        <div class="w-fit mx-auto space-y-5">

            {{-- Confirmation Message --}}
            <p class="text-sm text-gray-600 text-center">
                Are you sure you want to archive this staff?
            </p>

            {{-- Staff Information --}}
            <x-card color="red" class="w-full">
                
                <div class="space-y-2">

                    {{-- Full Name --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            ID No:
                        </span>

                        <span class="text-xs font-medium">
                            STF-00001
                        </span>
                    </div>

                    {{-- Full Name --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Full Name:
                        </span>

                        <span class="text-xs font-medium">
                            Galanida, Filemon Jr., Leornas
                        </span>
                    </div>

                    {{-- Account Status --}}
                    <div class="grid grid-cols-[100px_1fr] gap-2 items-start">
                        <span class="text-xs font-bold">
                            Account Status:
                        </span>

                        <span class="text-xs font-medium">
                            De-activated
                        </span>
                    </div>

                </div>

            </x-card>

            {{-- Buttons --}}
            <div class="flex justify-center gap-2">

                <x-button
                    type="button"
                    color="outline-gray"
                    data-modal-close="archive-confirmation-modal"
                >
                    No, Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-red"
                    onclick="confirmArchivedStaff()"
                >
                    Yes, Archive
                </x-button>

            </div>

        </div>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="archive-staff-loading" text="Archiving staff..." />

    {{-- Success Modal --}}
    <x-success_modal id="archive-staff-success" text="Staff added to archives successfully!" />
@endsection
