@extends('layouts.app')

@section('title', 'Offices')

@section('content')

    <script src="{{ asset('assets/js/office.js') }}"></script>

    <div class="space-y-6">
            
        {{-- Search & Filters --}}
        <x-card>

            <div class="flex grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-overview_card
                    icon="ti ti-building-bank"
                    label="Offices"
                    total="1"
                    color="blue"
                />

                <x-overview_card
                    icon="ti ti-building"
                    label="Divisions"
                    total="0"
                    color="d-blue"
                />

            </div> 
            
            <div class="border-t border-gray-200 mt-4 mb-4"></div>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:w-auto">

                    {{-- Search --}}
                    <x-input_white
                        name="search"
                        type="text"
                        placeholder="Search by name or id"
                        icon="ti ti-search"
                        class="w-full sm:w-[20rem]"
                    />

                    {{-- Search Button --}}
                    <x-button
                        color="d-blue"
                        type="button"
                        class="h-9 w-full lg:inline-flex sm:w-auto text-xs"
                    >
                        Search
                    </x-button>



                </div>

                {{-- Desktop Add Staff --}}
                <x-button
                    color="d-blue"
                    type="button"
                    icon="ti ti-building-plus"
                    class="hidden h-9 lg:inline-flex lg:w-auto text-xs"
                    data-modal-open="add-office"
                >
                    Add Office
                </x-button>

            </div>

        </x-card>


        {{-- Staff Table --}}
        <x-card>

            <x-table
                :columns="[
                    'ID',
                    'Offices',
                    'Divisions',
                ]"
                :actions="true"
            >

                <x-slot:body>


                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            OFF-00001
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            CDRRMO
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            0
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-eye"
                                    color="blue"
                                    title="View Office"
                                    href="{{ route('admin.offices.office_divisions') }}"
                                />
                            </div>
                        </td>
                    </tr>

                </x-slot:body>

            </x-table>

        </x-card>

    </div>

    <x-modal_form
        id="add-office"
        title="Add Office"
        icon="ti ti-building-bank"
        width="max-w-sm"
    >

        <form
            action="{{ route('admin.offices')}}"
        >
            <x-input_white
                name="office"
                type="text"
                placeholder="Office Name"
                label="Office Name"
            />
            <p id="office-error" class="text-red-500 text-xs mt-1 hidden">Office name is required</p>

            {{-- Buttons --}}
            <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="showConfirmOfficeName()"
                    >
                    Proceed
                </x-button>

            </div>
        </form>

    </x-modal_form>


    <x-modal_form
        id="add-office-confirmation"
        title="Add Office"
        icon="ti ti-building-bank"
        width="max-w-sm"
    >

        <div class="text-center space-y-2">
            <span class="text-gray-600 text-xs">Are you sure you want to add this Office?</span>
            <p class="text-sm font-medium text-gray-900">Office Name: <span id="confirm-office-name" method="POST"></span></p>
        </div>
            
            {{-- Buttons --}}
            <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="add-office-confirmation"
                    data-modal-open="add-office"
                >
                    No, Back
                </x-button>

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="confirmAddOffice()"
                    >
                    Yes, Confirm
                </x-button>

            </div>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="add-office-loading" text="Adding new Office..." />

    {{-- Success Modal --}}
    <x-success_modal id="add-office-success" text="New Office added successfully" />
@endsection