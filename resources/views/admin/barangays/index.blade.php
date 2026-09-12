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
                    :total="$barangays->count()"
                    color="blue"
                    id="total-barangays"
                />

                <x-overview_card
                    icon="ti ti-key"
                    label="Active Keys"
                    :total="$barangays->where('key_status', 'Active')->count()"
                    color="green"
                    id="active-keys"
                />

                <x-overview_card
                    icon="ti ti-key-off"
                    label="Disabled Keys"
                    :total="$barangays->where('key_status', 'Disabled')->count()"
                    color="red"
                    id="deactivated-keys"
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

                    @forelse ($barangays as $barangay)
                    <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ $barangay->barangay_ref_num }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            {{ $barangay->barangay_name }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                            @if ($barangay->key_status === 'Active')
                                <x-badge
                                    color="green"
                                    label="Active"
                                    icon="ti ti-circle-check"
                                />
                            @else
                                <x-badge
                                    color="red"
                                    label="Disabled"
                                    icon="ti ti-circle-x"
                                />
                            @endif
                        </td>

                        {{-- Buttons --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <x-action_btn
                                    icon="ti ti-refresh"
                                    color="d-blue"
                                    title="Update Barangay"
                                    data-modal-open="update-barangay-modal"
                                    data-id="{{ $barangay->barangay_id }}"
                                    data-name="{{ $barangay->barangay_name }}"
                                    data-status="{{ $barangay->key_status }}"
                                />

                                <x-action_btn
                                    icon="ti ti-eye"
                                    color="blue"
                                    title="View Barangay"
                                    data-modal-open="barangay-information-modal"
                                    data-ref="{{ $barangay->barangay_ref_num }}"
                                    data-name="{{ $barangay->barangay_name }}"
                                    data-key="{{ $barangay->secret_key_hash }}"
                                    data-status="{{ $barangay->key_status }}"
                                />

                                
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                            No barangays found.
                        </td>
                    </tr>
                    @endforelse

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
        data-modal-static
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-lg flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">

            {{-- Success Icon --}}
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <i class="ti ti-circle-check text-4xl text-green-500"></i>
            </div>

            {{-- Message --}}
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">New Barangay Added Successfully!</h3>

            {{-- Barangay Info --}}
                <div class="w-full rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-500">Reference Number</span>
                        <span id="success-barangay-ref" class="text-xs font-semibold text-gray-900"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-xs text-gray-500">Barangay Name</span>
                        <span id="success-barangay-name" class="text-xs font-semibold text-gray-900"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-xs text-gray-500">Secret Key</span>
                        <span id="success-secret-key" class="text-xs font-semibold text-gray-900"></span>
                    </div>
                </div>

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
                <span id="view-barangay-ref" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Barangay Name:</span>
                <span id="view-barangay-name" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Secret Key:</span>
                <span id="view-barangay-key" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 ">
                <span class="text-xs text-gray-500">Key Status:</span>
                <span id="view-barangay-status"></span>
            </div>
        </x-card>

    </x-modal_form>

    {{-- Update Barangay Modal --}}
    <x-modal_form
        id="update-barangay-modal"
        title="Update Barangay"
        icon="ti ti-refresh"
        width="max-w-sm"
    >
        <form onsubmit="return false;">
            <input type="hidden" name="barangay_id" id="update-barangay-id">

            <x-input_white
                name="barangay_name"
                type="text"
                placeholder="Barangay Name"
                label="Barangay Name"
                id="update-barangay-name"
                :editable="false"
            />
            <p id="update-name-error" class="text-red-500 text-xs mt-1 hidden"></p>

            <div class="mt-3">
                <x-dropdown
                    name="key_status"
                    placeholder="Select Status"
                    size="md"
                    label="Key Status"
                    :options="[
                        'Active' => 'Active',
                        'Disabled' => 'Disabled',
                    ]"
                />
                <p id="update-status-error" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div class="flex w-full items-center justify-end gap-3 mt-4">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="update-barangay-modal"
                >
                    Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="submitUpdateBarangay()"
                >
                    Update
                </x-button>
            </div>
        </form>
    </x-modal_form>

    {{-- Update Loading Modal --}}
    <x-loading_modal id="update-barangay-loading" text="Updating barangay..." />

    {{-- Update Success Modal --}}
    <x-success_modal id="update-barangay-success" text="Barangay updated successfully!" />
    
@endsection
