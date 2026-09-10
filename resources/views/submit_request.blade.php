@extends('layouts.track_submit')

@section('title', 'Submit Request')

@section('content')

    <script src="{{ asset('assets/js/submit.js') }}"></script>

    <main class="w-[calc(100%-2rem)] sm:w-[36rem] h-fit px-6 py-6 sm:px-10 sm:py-8 bg-[#020d1d] rounded-xl mx-auto my-4 sm:my-8 shadow-2xl shadow-black/60">
        <div class="flex justify-start">
            <a
                href="{{ route('home') }}"
                title="Back"
                class="flex items-center gap-1.5 h-9 px-2.5 rounded-lg text-white hover:bg-white/10 transition-colors"
            >
                <i class="ti ti-arrow-narrow-left text-xl"></i>
                <span class="text-xs font-light"> Return </span>
            </a>
        </div>

        <div class="flex items-center justify-center w-15 h-15 mx-auto mt-2 rounded-xl bg-[#071827] text-[#2c51ec]">
            <i class="ti ti-send text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-center text-white mt-2">Submit a Ticket</h1>
        <div class="flex justify-center items-center mt-2">
            <p class="text-center text-xs text-gray-400">
                Track your submitted ticket. 
            </p>

            <a
                href="{{ route('track.request') }}"
                class="text-xs font-semibold text-[#2c51ec] ml-2"
            >
                Click Here.
            </a>

        </div>

        {{-- Request Type Selection --}}
        <div class="mt-4">
            <label class="text-xs font-semibold text-white mb-2 block">Please select</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2.5 rounded-lg border border-gray-700 bg-[#071827] hover:bg-[#0d2341] transition-colors has-[:checked]:border-[#2c51ec] has-[:checked]:bg-[#2c51ec]/10">
                    <input type="radio" name="request_type" value="barangay" id="radio-barangay" checked
                        class="w-4 h-4 text-[#2c51ec] bg-[#071827] border-gray-500 focus:ring-[#2c51ec] focus:ring-2 cursor-pointer"
                        onchange="toggleRequestType('barangay')">
                    <span class="text-sm text-white font-medium">Barangay</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2.5 rounded-lg border border-gray-700 bg-[#071827] hover:bg-[#0d2341] transition-colors has-[:checked]:border-[#2c51ec] has-[:checked]:bg-[#2c51ec]/10">
                    <input type="radio" name="request_type" value="city_office" id="radio-city-office"
                        class="w-4 h-4 text-[#2c51ec] bg-[#071827] border-gray-500 focus:ring-[#2c51ec] focus:ring-2 cursor-pointer"
                        onchange="toggleRequestType('city_office')">
                    <span class="text-sm text-white font-medium">City Office</span>
                </label>
            </div>
        </div>

        {{-- Barangay Container --}}
        <div id="barangay-container" class="space-y-4 mt-4 p-4 rounded-xl border border-gray-700 bg-[#071827]/50">
            <h2 class="text-sm font-semibold text-[#2c51ec] mb-4">
                <i class="ti ti-building-community mr-1"></i> Barangay Request
            </h2>

            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Barangay --}}
                <x-dropdown
                    name="barangay"
                    placeholder="Select Barangay"
                    label="Barangay"
                    :options="[
                        'barangay1' => 'Barangay 1',
                        'barangay2' => 'Barangay 2',
                        'barangay3' => 'Barangay 3',
                        'barangay4' => 'Barangay 4',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

                {{-- Category --}}
                <x-dropdown
                    name="brgy_category"
                    placeholder="Select Category"
                    label="Category"
                    :options="[
                        'hardware' => 'Hardware',
                        'software' => 'Software',
                        'network' => 'Network',
                        'other' => 'Other',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

            </div>

            <div>
                {{-- Issue --}}
                <x-dropdown
                    name="brgy_issue"
                    placeholder="Select Issue"
                    label="Issue"
                    :options="[
                        'not_working' => 'Not Working',
                        'slow' => 'Slow Performance',
                        'error' => 'Error/Crash',
                        'setup' => 'Setup/Installation',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />
            </div>

            <div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-white">Description </h1>
                    </label>
                    <textarea
                        name="brgy_description"
                        rows="6"
                        placeholder="Provide a detailed description of your request..."
                        class="w-full rounded-lg border border-gray-700 bg-[#071827] px-3 py-2.5 text-sm font-regular text-white placeholder-gray-500 outline-none transition focus:border-[#2c51ec] focus:ring-2 focus:ring-[#2c51ec]/30 resize-y"
                    ></textarea>
                </div>

                {{-- Secret Key --}}
                <div class="md:col-span-2 mt-4">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-white">Secret Key</h1>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="brgy_secret_key"
                            id="brgy_secret_key"
                            placeholder="Enter your barangay's secret key"
                            class="w-full rounded-lg border border-gray-700 bg-[#071827] px-3 py-2.5 pr-10 text-sm font-regular text-white placeholder-gray-500 outline-none transition focus:border-[#2c51ec] focus:ring-2 focus:ring-[#2c51ec]/30"
                        />
                        <button
                            type="button"
                            onclick="togglePassword('brgy_secret_key')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- City Office Container --}}
        <div id="city-office-container" class="mt-4 p-4 rounded-xl border border-gray-700 bg-[#071827]/50 hidden">
            <h2 class="text-sm font-semibold text-[#2c51ec] mb-4">
                <i class="ti ti-building-bank mr-1"></i> City Office Request
            </h2>
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Departments/Offices --}}
                <x-dropdown
                    name="department_office"
                    placeholder="Select Department/Office"
                    label="Departments/Offices"
                    :options="[
                        'dept1' => 'Department 1',
                        'dept2' => 'Department 2',
                        'office1' => 'Office 1',
                        'office2' => 'Office 2',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

                {{-- Division --}}
                <x-dropdown
                    name="division"
                    placeholder="Select Division"
                    label="Division"
                    :options="[
                        'div1' => 'Division 1',
                        'div2' => 'Division 2',
                        'div3' => 'Division 3',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

                {{-- Category --}}
                <x-dropdown
                    name="city_category"
                    placeholder="Select Category"
                    label="Category"
                    :options="[
                        'hardware' => 'Hardware',
                        'software' => 'Software',
                        'network' => 'Network',
                        'other' => 'Other',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

                {{-- Issue --}}
                <x-dropdown
                    name="city_issue"
                    placeholder="Select Issue"
                    label="Issue"
                    :options="[
                        'not_working' => 'Not Working',
                        'slow' => 'Slow Performance',
                        'error' => 'Error/Crash',
                        'setup' => 'Setup/Installation',
                    ]"
                    backgroundColor="#071827"
                    borderColor="#374151"
                    focusColor="#2c51ec"
                    textColor="#ffffff"
                    placeholderColor="#6b7280"
                    hoverColor="#0d2341"
                    iconColor="#6b7280"
                />

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-white">Description </h1>
                    </label>
                    <textarea
                        name="city_description"
                        rows="6"
                        placeholder="Provide a detailed description of your request..."
                        class="w-full rounded-lg border border-gray-700 bg-[#071827] px-3 py-2.5 text-sm font-regular text-white placeholder-gray-500 outline-none transition focus:border-[#2c51ec] focus:ring-2 focus:ring-[#2c51ec]/30 resize-y"
                    ></textarea>
                </div>

                {{-- Secret Key --}}
                <div class="md:col-span-2">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-white">Secret Key</h1>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="city_secret_key"
                            id="city_secret_key"
                            placeholder="Enter your office's secret key"
                            class="w-full rounded-lg border border-gray-700 bg-[#071827] px-3 py-2.5 pr-10 text-sm font-regular text-white placeholder-gray-500 outline-none transition focus:border-[#2c51ec] focus:ring-2 focus:ring-[#2c51ec]/30"
                        />
                        <button
                            type="button"
                            onclick="togglePassword('city_secret_key')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-6 flex justify-end">
            <x-button
                type="button"
                color="d-blue"
                icon="ti ti-send"
                iconPosition="right"
                onclick="openConfirmModal()"
            >
                Submit Ticket
            </x-button>
        </div>
    </main>


    {{-- Barangay Confirmation Modal --}}
    <x-modal_form
        id="confirm-barangay-modal"
        title="Confirm Barangay Submission"
        icon="ti ti-alert-circle"
        width="max-w-sm"
    >
        <div>
            <p class="text-xs text-center text-gray-600 mb-4">Please review your barangay ticket details before submitting</p>

            <div class="w-full rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5 text-left">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Barangay</span>
                    <span id="brgy-confirm-dept" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Category</span>
                    <span id="brgy-confirm-category" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Issue</span>
                    <span id="brgy-confirm-issue" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="py-2">
                    <span class="text-xs text-gray-500 block mb-1">Details</span>
                    <p id="brgy-confirm-details" class="text-xs font-semibold text-gray-900 text-justify break-words whitespace-normal m-0">-</p>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <x-button
                    type="button"
                    color="outline-gray"
                    data-modal-close="confirm-barangay-modal"
                >
                    Cancel
                </x-button>
                <x-button
                    type="button"
                    color="outline-blue"
                    icon="ti ti-check"
                    data-modal-close="confirm-barangay-modal"
                    data-modal-open="loading-submit-modal"
                >
                    Confirm
                </x-button>
            </div>
        </div>
    </x-modal_form>

    {{-- City Office Confirmation Modal --}}
    <x-modal_form
        id="confirm-office-modal"
        title="Confirm Office Submission"
        icon="ti ti-alert-circle"
        width="max-w-sm"
    >
        <div>
            <p class="text-xs text-center text-gray-600 mb-4">Please review your office ticket details before submitting</p>

            <div class="w-full rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5 text-left">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Department/Office</span>
                    <span id="office-confirm-dept" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Division</span>
                    <span id="office-confirm-division" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Category</span>
                    <span id="office-confirm-category" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Issue</span>
                    <span id="office-confirm-issue" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="py-2">
                    <span class="text-xs text-gray-500 block mb-1">Details</span>
                    <p id="office-confirm-details" class="text-xs font-semibold text-gray-900 text-justify break-words whitespace-normal m-0">-</p>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <x-button
                    type="button"
                    color="outline-gray"
                    data-modal-close="confirm-office-modal"
                >
                    Cancel
                </x-button>
                <x-button
                    type="button"
                    color="outline-blue"
                    icon="ti ti-check"
                    data-modal-close="confirm-office-modal"
                    data-modal-open="loading-submit-modal"
                >
                    Confirm
                </x-button>
            </div>
        </div>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="loading-submit-modal" text="Submitting your ticket..." />

    {{-- Success Modal --}}
    <div
        id="success-submit-modal"
        data-modal
        data-modal-static
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">

            {{-- Success Icon --}}
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <i class="ti ti-circle-check text-4xl text-green-500"></i>
            </div>

            {{-- Message --}}
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">Ticket Submitted Successfully!</h3>
            <p class="text-xs text-gray-500 text-center mb-5">Please save your ticket or take a screenshot.</p>

            {{-- Ticket Info --}}
            <div class="w-full rounded-lg bg-gray-50 border border-gray-200 p-4 mb-5">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-xs text-gray-500">Ticket Number</span>
                    <span id="ticket-number" class="text-xs font-semibold text-gray-900">TKT-2026-00001</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-xs text-gray-500">Date Submitted</span>
                    <span id="date-submitted" class="text-xs font-semibold text-gray-900"></span>
                </div>
            </div>

            {{-- Download Button --}}
            <x-button
                type="button"
                color="d-blue"
                icon="ti ti-download"
                class="w-full mb-3"
                onclick="downloadTicketPDF()"
            >
                Download as PDF
            </x-button>

            {{-- Close Button --}}
            <x-button
                type="button"
                color="gray"
                data-modal-close="success-submit-modal"
                class="w-full"
            >
                Close
            </x-button>

        </div>
    </div>


@endsection
