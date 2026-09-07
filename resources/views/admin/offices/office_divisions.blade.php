@extends('layouts.app')

@section('title', 'Divisions')

@section('content')

<script src="{{asset('assets/js/division.js')}}"></script>

<div class="space-y-6">

    <x-card>
        {{-- Return Button --}}
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <x-button
                    color="outline-dark"
                    iconPosition="center"
                    icon="ti ti-arrow-narrow-left"
                    href="{{ route('admin.offices') }}"
                    class="w-fit text-xs"
                >
                    Return
                </x-button>

                <span class="text-lg text-black fond-bold"> Office Name </span>
            </div>

            <div class="flex grid grid-cols-1 md:grid-cols-3 gap-6 mt-5">
                <x-overview_card
                    icon="ti ti-building"
                    label="Divisions"
                    total="5"
                    color="blue"
                />

                <x-overview_card
                    icon="ti ti-key"
                    label="Active Keys"
                    total="0"
                    color="green"
                />

                <x-overview_card
                    icon="ti ti-key-off"
                    label="Disabled Keys"
                    total="0"
                    color="red"
                />
            </div> 

            <div class="border-t border-gray-200 mb-4 mt-4"></div>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mt-4">
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
                <x-button
                    color="d-blue"
                    type="button"
                    icon="ti ti-building-plus"
                    data-modal-open="add-division"
                >
                    Add Division
                </x-button>
            </div>
    </x-card>

    <x-card>
        <x-table
                :columns="[
                    'ID',
                    'Divisions',
                    'Key Status'
                ]"
                :actions="true"
            >

                <x-slot:body>


                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            DIV-00001
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            Example
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            <x-badge
                                icon="ti ti-circle-check"
                                color="green"
                                label="Active"
                            />
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-refresh"
                                    color="blue"
                                    title="Update Division"
                                />
                            </div>
                        </td>
                    </tr>

                </x-slot:body>

            </x-table>
    </x-card>
</div>

<x-modal_form
    id="add-division"
    title="Add Disivion"
    icon="ti ti-building-plus"
    width="max-w-sm"
>

    <form
        action="{{ route('admin.offices.office_divisions')}}"
    >
        <x-input_white
            name="division"
            type="text"
            placeholder="Example"
            label="Division Name"
        />
        <p id="division-error" class="text-red-500 text-xs mt-1 hidden">Division name is required</p>

        {{-- Buttons --}}
        <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

            <x-button
                type="button"
                color="outline-green"
                onclick="showConfirmDivisionName()"
                >
                Proceed
            </x-button>

        </div>
    </form>
</x-modal_form>

<x-modal_form
    id="add-division-confirmation"
    title="Add Division"
    icon="ti ti-building-plus"
    width="max-w-sm"
>

    <div class="text-center space-y-2">
        <span class="text-gray-600 text-xs">Are you sure you want to add this division?</span>
        <p class="text-sm font-medium text-gray-900">Division Name: <span id="confirm-division-name"></span></p>
    </div>
        
        {{-- Buttons --}}
        <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

            <x-button
                type="button"
                color="outline-red"
                data-modal-close="add-division-confirmation"
                data-modal-open="add-division"
            >
                No, Back
            </x-button>

            <x-button
                type="button"
                color="outline-green"
                onclick="confirmAddDivision()"
                >
                Yes, Confirm
            </x-button>

        </div>
</x-modal_form>

{{-- Success Modal --}}
<div
    id="add-division-success"
    data-modal
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
>
    <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">

        {{-- Success Icon --}}
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
            <i class="ti ti-circle-check text-4xl text-green-500"></i>
        </div>

        {{-- Message --}}
        <h3 class="text-base font-semibold text-gray-900 text-center mb-1">New Division Added Successfully!</h3>

        {{-- Division Info --}}
        <form action="{{ route('admin.offices.office_divisions') }}" method="POST" class="w-full mt-6">
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Division Name</span>
                    <span id="division_name" class="text-xs font-semibold text-gray-900"></span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-xs text-gray-500">Secret Key</span>
                    <span id="secret_key" class="text-xs font-semibold text-gray-900">123123asdasd</span>
                </div>
            </div>

        </form>

        {{-- Close Button --}}
        <x-button
            type="button"
            color="gray"
            data-modal-close="add-division-success"
            class="w-full"
        >
            Close
        </x-button>

    </div>
</div>

{{-- Loading Modal --}}
<x-loading_modal id="add-division-loading" text="Adding division..." />

<x-loading_modal id="generating-key-loading" text="Generating divisions Secret key..." />


    
@endsection