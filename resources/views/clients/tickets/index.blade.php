@extends('layouts.app')

@section('title', 'Tickets')

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <div class="space-y-6">
        <x-card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <x-input_white
                        name="search"
                        type="text"
                        placeholder="Search by ticket refrence number"
                        icon="ti ti-search"
                        class="w-full sm:w-[20rem]"
                    />
                    <x-button
                        color="d-blue"
                        type="button"
                        class="w-full sm:w-auto h-9 text-xs"
                    >
                        Search
                    </x-button>
                </div>
            </div>
        </x-card>

        <x-card>

            <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a data-modal-open="ticket-info-modal" class="block">
                    <x-card class="transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <x-icon_container
                                icon="ti ti-ticket"
                                color="blue"
                            />
                            <div class="grid grid-cols-1 gap-1">
                                <span class="text-lg font-semibold">Hardware</span>
                                <div class="flex flex-col gap-1">
                                    <x-badge
                                    icon="ti ti-alert-triangle"
                                    color="red"
                                    label="Critical"
                                    />
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex inline-flex gap-2 mt-3">
                            <span class="text-xs text-gray-400 font-semibold"> Reference No: </span>
                            <span class="text-xs text-black"> TCKT-00001 </span>
                        </div>
                        <div class="flex inline-flex gap-2">
                            <span class="text-xs text-gray-400 font-semibold"> Requester: </span>
                            <span class="text-xs text-black"> Barangay - Amihan </span>
                        </div>
                        <div class="flex inline-flex gap-2 text-center align-center">
                            <span class="text-xs text-gray-400 font-semibold"> Status: </span>
                            <x-badge 
                                icon="ti ti-clock"
                                color="orange"
                                label="Pending"
                                width="max-w-sm"
                            />
                        </div>
                    </x-card>
                </a>
            </div>
        </x-card>
    </div>

    <x-modal_form
        id="ticket-info-modal"
        icon="ti ti-ticket"
        title="Ticket Information"
        width="max-w-sm"
    >

        <x-card 
            color="gray" class="w-full p-4"
        >
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Date Assigned:</span>
                <span id="modal-date" class="text-xs font-semibold text-gray-900">09/17/2026</span>
            </div>
            
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Requester:</span>
                <p id="modal-date" class="text-xs font-semibold text-gray-900">City Disaster Risk Management Office</p>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Category:</span>
                <span id="modal-date" class="text-xs font-semibold text-gray-900">Hardware</span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Issue:</span>
                <span id="modal-date" class="text-xs font-semibold text-gray-900">Black Screen</span>
            </div>

            
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Priority:</span>
                <x-badge
                color="red"
                icon="ti ti-alert-triangle"
                label="Critical"
                />
            </div>

            <div class="py-2">
                <span class="text-xs text-gray-500 block mb-1">Details</span>
                <p id="brgy-confirm-details" class="text-xs font-semibold text-gray-900 text-justify break-words whitespace-normal m-0">-</p>
            </div>

        </x-card>
        
        <div class="flex justify-center gap-3 mt-5">
                <x-button
                    color="outline-red"
                    icon="ti ti-x"
                    iconPosition="right"
                >
                    Request Reassignment
                </x-button>

                <x-button
                    color="outline-green"
                    icon="ti ti-refresh"
                    iconPosition="right"
                >
                    Update
                </x-button>
        </div>

    </x-modal_form>
@endsection