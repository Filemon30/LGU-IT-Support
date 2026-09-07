@extends('layouts.app')

@section('title', 'Barangays')

@section('content')

    <script src="{{asset('assets/js/modal.js')}}"></script>
    <script src="{{asset('assets/js/barangay.js')}}"></script>
    <script src="{{asset('assets/js/submit.js')}}"></script>

    <div class="space-y-6">
            
        {{-- Search & Filters --}}
        <x-card>

            <div class="flex grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-overview_card
                    icon="ti ti-building-bank"
                    label="Barangays"
                    total="2"
                    color="blue"
                />

                <x-overview_card
                    icon="ti ti-key"
                    label="Active Keys"
                    total="1"
                    color="green"
                />

                <x-overview_card
                    icon="ti ti-key-off"
                    label="Deactivated Keys"
                    total="0"
                    color="red"
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
                        class="w-full h-9 lg:inline-flex sm:w-auto text-xs"
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
                        class="w-full h-8 sm:ml-4 sm:w-[10rem]"
                    />


                </div>

                <x-button
                    color="d-blue"
                    type="button"
                    icon="ti ti-building-plus"
                    class="hidden h-9 lg:inline-flex lg:w-auto text-xs"
                    data-modal-open="add-barangay"
                >
                    Add Barangay
                </x-button>

            </div>

        </x-card>


        {{-- Barangay Table --}}
        <x-card>

            <x-table
                :columns="[
                    'ID',
                    'Barangay',
                    'Key Status',
                ]"
                :actions="true"
            >

                <x-slot:body>

                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            BRGY-00001
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            Barangay Amihan
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            <x-badge
                                color="green"
                                label="Active"
                                icon="ti ti-circle-check"
                            />
                        </td>

                        {{-- Buttons --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-refresh"
                                    color="d-blue"
                                    title="Update Barangay"
                                />

                                <x-action_btn
                                    icon="ti ti-eye"
                                    color="blue"
                                    title="View Barangay"
                                    data-modal-open="barangay-information-modal"
                                />

                                
                            </div>
                        </td>
                    </tr>

                    

                </x-slot:body>

            </x-table>

        </x-card>

    </div>

    <x-modal_form
        id="add-barangay"
        title="Add Barangay"
        icon="ti ti-building-community"
        width="max-w-sm"
    >

        <form
            action="{{ route('admin.barangays')}}"
        >
            <x-input_white
                name="barangay"
                type="text"
                placeholder="Amihan"
                label="Barangay Name"
            />
            <p id="barangay-error" class="text-red-500 text-xs mt-1 hidden">Barangay name is required</p>

            {{-- Buttons --}}
            <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="showConfirmBarangayName()"
                    >
                    Proceed
                </x-button>

            </div>
        </form>
    </x-modal_form>


    <x-modal_form
        id="add-barangay-confirmation"
        title="Add Barangay"
        icon="ti ti-building-community"
        width="max-w-sm"
    >

        <div class="text-center space-y-2">
            <span class="text-gray-600 text-xs">Are you sure you want to add this barangay?</span>
            <p class="text-sm font-medium text-gray-900">Barangay Name: <span id="confirm-barangay-name">Amihan</span></p>
        </div>
            
            {{-- Buttons --}}
            <div class="col-span-1 md:col-span-3 flex w-full items-center justify-end gap-3 mt-3">

                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="add-barangay-confirmation"
                    data-modal-open="add-barangay"
                >
                    No, Back
                </x-button>

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="confirmAddBarangay()"
                    >
                    Yes, Confirm
                </x-button>

            </div>
    </x-modal_form>

    {{-- Success Modal --}}
    <div
        id="add-barangay-success"
        data-modal
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">

            {{-- Success Icon --}}
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <i class="ti ti-circle-check text-4xl text-green-500"></i>
            </div>

            {{-- Message --}}
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">New Barangay Added Successfully!</h3>

            {{-- Barangay Info --}}
            <form action="{{ route('admin.barangays') }}" method="POST" class="w-full mt-6">
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-500">Barangay Name</span>
                        <span id="ticket-number" class="text-xs font-semibold text-gray-900">Amihan</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-xs text-gray-500">Secret Key</span>
                        <span id="date-submitted" class="text-xs font-semibold text-gray-900">JKAHSDKJ192390</span>
                    </div>
                </div>

            </form>

            {{-- Close Button --}}
            <x-button
                type="button"
                color="gray"
                data-modal-close="add-barangay-success"
                class="w-full"
            >
                Close
            </x-button>

        </div>
    </div>

    {{-- Loading Modal --}}
    <x-loading_modal id="add-barangay-loading" text="Adding barangay..." />

    <x-loading_modal id="generating-key-loading" text="Generating Barangay Secret key..." />

    <x-modal_form
        id="barangay-information-modal"
        title="Barangay Information"
        icon="ti ti-building-community"
        width="max-w-lg"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >

        <x-card color="dark" class="w-full p-4 ">
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Barangay ID:</span>
                <span id="ticket-number" class="text-xs font-semibold text-gray-900">BRGY-00001</span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Barangay Name:</span>
                <span id="date-submitted" class="text-xs font-semibold text-gray-900">Amihan</span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Secret key:</span>
                <span id="date-submitted" class="text-xs font-semibold text-gray-900">KJA9081239ASD</span>
            </div>

            <div class="flex justify-between py-2 ">
                <span class="text-xs text-gray-500">Key Status:</span>
                <x-badge 
                    color="green"
                    icon="ti ti-circle-check"
                    label="Active"    
                />
            </div>
        </x-card>

    </x-modal_form>
    
@endsection
