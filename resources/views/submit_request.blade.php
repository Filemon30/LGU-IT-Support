@extends('layouts.track_submit')

@section('title', 'Submit Request')

@section('content')

    <main class="w-[calc(100%-2rem)] sm:w-[36rem] h-fit px-6 py-6 sm:px-10 sm:py-8 bg-white rounded-xl mx-auto my-4 sm:my-8 shadow-lg border border-gray-200">
        <div class="flex justify-start">
            <a
                href="{{ route('home') }}"
                title="Back"
                class="flex items-center gap-1.5 h-9 px-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
            >
                <i class="ti ti-arrow-narrow-left text-xl"></i>
                <span class="text-xs font-light"> Return </span>
            </a>
        </div>

        <div class="flex items-center justify-center w-15 h-15 mx-auto mt-2 rounded-xl bg-blue-50 text-blue-600">
            <i class="ti ti-send text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-center text-gray-900 mt-2">Submit a Ticket</h1>
        <div class="flex justify-center items-center mt-2">
            <p class="text-center text-xs text-gray-500">
                Track your submitted ticket.
            </p>

            <a
                href="{{ route('track.request') }}"
                class="text-xs font-semibold text-blue-600 ml-2"
            >
                Click Here.
            </a>

        </div>

        {{-- Request Type Selection --}}
        <div class="mt-4">
            <label class="text-xs font-semibold text-gray-700 mb-2 block">Please select</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="request_type" value="barangay" id="radio-barangay" checked
                        class="w-4 h-4 text-blue-600 bg-white border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer"
                        onchange="toggleRequestType('barangay')">
                    <span class="text-sm text-gray-700 font-medium">Barangay</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="request_type" value="city_office" id="radio-city-office"
                        class="w-4 h-4 text-blue-600 bg-white border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer"
                        onchange="toggleRequestType('city_office')">
                    <span class="text-sm text-gray-700 font-medium">City Office</span>
                </label>
            </div>
        </div>

        {{-- Barangay Container --}}
        <div id="barangay-container" class="space-y-4 mt-4 p-4 rounded-xl border border-gray-200 bg-gray-50">
            <h2 class="text-sm font-semibold text-blue-600 mb-4">
                <i class="ti ti-building-community mr-1"></i> Barangay Request
            </h2>

            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Barangay --}}
                <div>
                    <x-dropdown
                        name="barangay"
                        placeholder="Select Barangay"
                        label="Barangay"
                        :options="$barangays->pluck('barangay_name', 'barangay_id')->toArray()"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-barangay" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Category --}}
                <div>
                    <x-dropdown
                        name="brgy_category"
                        placeholder="Select Category"
                        label="Category"
                        :options="$categories->pluck('category_name', 'category_id')->toArray()"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-brgy_category" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

            </div>

            <div>
                {{-- Issue --}}
                <div>
                    <x-dropdown
                        name="brgy_issue"
                        placeholder="Select category first"
                        label="Issue"
                        :options="[]"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-brgy_issue" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>
            </div>

            <div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-gray-700">Description </h1>
                    </label>
                    <textarea
                        name="brgy_description"
                        rows="6"
                        placeholder="Provide a detailed description of your request..."
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-y"
                    ></textarea>
                    <p id="error-brgy_description" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Secret Key --}}
                <div class="md:col-span-2 mt-4">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-gray-700">Secret Key</h1>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="brgy_secret_key"
                            id="brgy_secret_key"
                            placeholder="Enter your barangay's secret key"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 pr-10 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                        <button
                            type="button"
                            onclick="togglePassword('brgy_secret_key')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <i class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                    <p id="error-brgy_secret_key" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

            </div>
        </div>

        {{-- City Office Container --}}
        <div id="city-office-container" class="mt-4 p-4 rounded-xl border border-gray-200 bg-gray-50 hidden">
            <h2 class="text-sm font-semibold text-blue-600 mb-4">
                <i class="ti ti-building-bank mr-1"></i> City Office Request
            </h2>
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Departments/Offices --}}
                <div>
                    <x-dropdown
                        name="department_office"
                        placeholder="Select Department/Office"
                        label="Departments/Offices"
                        :options="$offices->pluck('office_name', 'office_id')->toArray()"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-department_office" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Division --}}
                <div>
                    <x-dropdown
                        name="division"
                        placeholder="Select office first"
                        label="Division"
                        :options="[]"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-division" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Category --}}
                <div>
                    <x-dropdown
                        name="city_category"
                        placeholder="Select Category"
                        label="Category"
                        :options="$categories->pluck('category_name', 'category_id')->toArray()"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-city_category" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Issue --}}
                <div>
                    <x-dropdown
                        name="city_issue"
                        placeholder="Select category first"
                        label="Issue"
                        :options="[]"
                        backgroundColor="#ffffff"
                        borderColor="#d1d5db"
                        focusColor="#2563eb"
                        textColor="#111827"
                        placeholderColor="#9ca3af"
                        hoverColor="#f9fafb"
                        iconColor="#9ca3af"
                    />
                    <p id="error-city_issue" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="inline-flex flex-col mb-1">
                        <h1 class="text-xs font-semibold text-gray-700">Description</h1>
                        <h1 class="text-xs text-gray-400">Please provide section name to avoid ticket cancellation.</h1>
                    </label>
                    <textarea
                        name="city_description"
                        rows="6"
                        placeholder="Provide a detailed description of your request..."
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-y"
                    ></textarea>
                    <p id="error-city_description" class="text-xs text-red-500 mt-1 hidden"></p>
                    
                </div>

                {{-- Secret Key --}}
                <div class="md:col-span-2">
                    <label class="inline-flex mb-1">
                        <h1 class="text-xs font-semibold text-gray-700">Secret Key</h1>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="city_secret_key"
                            id="city_secret_key"
                            placeholder="Enter your office's secret key"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 pr-10 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                        <button
                            type="button"
                            onclick="togglePassword('city_secret_key')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <i class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                    <p id="error-city_secret_key" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>

            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-6 flex justify-end">
            <button
                type="button"
                id="btn-submit-ticket"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-[#071f45] px-4 h-9 text-xs font-medium text-white hover:bg-[#061a3a] transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#071f45]/30"
            >
                Submit Ticket
                <i class="ti ti-send"></i>
            </button>
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
                <button
                    type="button"
                    data-modal-close="confirm-barangay-modal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 h-9 text-xs font-medium text-gray-700 hover:bg-gray-100 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-100"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    data-action="confirm-submit"
                    data-type="barangay"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 h-9 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <i class="ti ti-check"></i>
                    Confirm
                </button>
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
                <button
                    type="button"
                    data-modal-close="confirm-office-modal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 h-9 text-xs font-medium text-gray-700 hover:bg-gray-100 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-100"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    data-action="confirm-submit"
                    data-type="office"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 h-9 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <i class="ti ti-check"></i>
                    Confirm
                </button>
            </div>
        </div>
    </x-modal_form>

    {{-- Loading Modal --}}
    <x-loading_modal id="loading-submit-modal" text="Submitting your ticket...." />

    {{-- Disabled Key Modal --}}
    <div
        id="disabled-key-modal"
        data-modal
        data-modal-static
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50">
                <i class="ti ti-key-off text-4xl text-red-500"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 text-center mb-1">Secret Key Disabled</h3>
            <p class="text-xs text-gray-500 text-center mb-5">Your secret key is disabled. Please contact the administrator.</p>
            <button
                type="button"
                data-modal-close="disabled-key-modal"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-gray-600 px-4 h-9 text-xs font-medium text-white hover:bg-gray-700 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-200 w-full"
            >
                Close
            </button>
        </div>
    </div>

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
                    <span id="ticket-number" class="text-xs font-semibold text-gray-900">-</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-xs text-gray-500">Date Submitted</span>
                    <span id="date-submitted" class="text-xs font-semibold text-gray-900"></span>
                </div>
            </div>

            {{-- Download Button --}}
            <button
                type="button"
                id="btn-download-pdf"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-[#071f45] px-4 h-9 text-xs font-medium text-white hover:bg-[#061a3a] transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#071f45]/30 w-full mb-3"
            >
                <i class="ti ti-download"></i>
                Download as PDF
            </button>

            {{-- Close Button --}}
            <button
                type="button"
                data-modal-close="success-submit-modal"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-gray-600 px-4 h-9 text-xs font-medium text-white hover:bg-gray-700 transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-200 w-full"
            >
                Close
            </button>

        </div>
    </div>

@endsection
