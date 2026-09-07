@extends('layouts.app')

@section('title', 'Staff Information')

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/staff.js') }}"></script>


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
                        color="green"
                        class="text-center ml-2 inline-flex"
                    >
                        Active
                    </x-badge>
                    
                </div>
            </div>


            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

                {{-- ID and Date Registered --}}
                <div class="flex flex-col md:flex-row md:items-end gap-3">

                    {{-- ID No. --}}
                    <x-input_white
                        name="id"
                        type="text"
                        label="ID No."
                        placeholder="IT-00001"
                        :editable="false"
                        class="w-fit"
                    />

                    {{-- Date Registered --}}
                    <x-input_white
                        name="registered"
                        type="text"
                        label="Date Registered"
                        placeholder="08/30/2026"
                        :editable="false"
                        class="w-fit"
                    />

                    <x-input_white
                    name="username"
                    type="text"
                    placeholder="admin"
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
                        data-modal-open="update-staff-modal"
                    >
                        Update
                    </x-button>

                    <x-button
                        color="outline-red"
                        icon="ti ti-archive"
                        iconPosition="left"
                        type="button"
                        data-modal-open="archive-confirmation-modal"
                    >
                        Archive
                    </x-button>

                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

                <x-input_white
                    name="last"
                    type="text"
                    placeholder="Last Name"
                    label="Last Name"
                    :editable="false"
                />

                <x-input_white
                    name="first"
                    type="text"
                    placeholder="First Name"
                    label="First Name"
                    :editable="false"
                />

                <x-input_white
                    name="middle"
                    type="text"
                    placeholder="Middle Name"
                    label="Middle Name"
                    :editable="false"
                    class="w-fit"
                />

                <x-input_white
                    name="suffix"
                    type="text"
                    placeholder="Select Suffix"
                    label="Suffix"
                    :editable="false"
                    class="max-w-full"
                />

                <x-input_white
                    name="birthdate"
                    type="text"
                    placeholder="mm/dd/yyyy"
                    label="Birthdate"
                    :editable="false"
                />

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                <x-input_white
                    name="gender"
                    type="text"
                    placeholder="Select Gender"
                    label="Gender"
                    :editable="false"
                    class="max-w-full"
                />

                <x-input_white
                    name="contact"
                    type="text"
                    placeholder="09876543210"
                    label="Contact No."
                    :editable="false"
                    class="w-fit"
                />

                <x-input_white
                    name="address"
                    type="text"
                    placeholder="Barangay Amihan, Biringan City, Encantadia"
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

    <x-modal_form
        id="update-staff-modal"
        title="Update Staff"
        icon="ti ti-user"
        width="max-w-sm"
    >

        
    </x-modal_form>


@endsection