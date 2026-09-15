@extends('layouts.track_submit')

@section('title', 'Track Request')

@section('content')

    <main class="w-[calc(100%-2rem)] max-w-md sm:max-w-none sm:w-fit h-fit px-6 py-6 sm:px-10 sm:py-8 bg-white rounded-xl mx-auto my-4 sm:my-8 shadow-lg border border-gray-200">

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
            <i class="ti ti-git-pull-request text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-center text-gray-900 mt-2">Track Your Ticket</h1>
        <p class="text-center text-xs text-gray-500 mt-2">Enter your reference number to check the status of your ticket.</p>
        
        <div class="flex justify-center items-center mt-2">
            <p class="text-center text-xs text-gray-500">
                Want to submit another ticket?
            </p>

            <a
                href="{{ route('submit.request') }}"
                class="text-xs font-semibold text-blue-600 ml-2"
            >
                Click Here.
            </a>

        </div>

        <form id="trackForm" class="w-full sm:w-[500px] mt-5 flex flex-col gap-2">
            <div class="flex flex-row items-center gap-3">
                <div style="flex: 1; position: relative;">
                    <x-input
                        id="refInput"
                        label="Reference Number"
                        placeholder="Enter Reference Number"
                        name="ref"
                        leftIcon="ti ti-ticket"
                        backgroundColor="#ffffff"
                        focusColor="#2563eb"
                        iconFocusColor="#2563eb"
                    />
                    <button
                        type="button"
                        id="clearInputBtn"
                        class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                        onclick="clearInput()"
                    >
                        <i class="ti ti-x text-sm"></i>
                    </button>
                </div>

                <x-button
                    color="blue"
                    type="submit"
                    class="h-10"
                >
                    Track
                </x-button>
            </div>
            <p id="refError" class="hidden text-xs text-red-500 flex items-center gap-1 ml-1">
                <i class="ti ti-alert-circle"></i>
                Reference number is required
            </p>
        </form>

        <div id="noResultMessage" class="hidden mt-6 p-6 bg-gray-50 border border-gray-200 rounded-xl text-center">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 text-gray-400">
                <i class="ti ti-search text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-700">No Ticket Information</p>
            <p class="text-xs text-gray-500 mt-1">No ticket found for this reference number. Please check and try again.</p>
        </div>

        <x-card id="resultCard" color="dark" label="Request Details" class="mt-6 hidden">
            <x-slot:actions>
                <button onclick="clearInput()" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </x-slot:actions>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Ticket Reference No.</p>
                    <p id="result-ref-num" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Date Requested</p>
                    <p id="result-date" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Requester Type</p>
                    <p id="result-requester-type" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Requester Name</p>
                    <p id="result-requester-name" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Category</p>
                    <p id="result-category" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Issue</p>
                    <p id="result-issue" class="text-sm font-semibold">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Priority</p>
                    <x-badge id="result-priority-badge" label="-" color="gray" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    <x-badge id="result-status-badge" label="-" color="gray" />
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Details</p>
                    <p id="result-description" class="text-sm font-semibold">-</p>
                </div>
                <div id="result-cancellation-row" class="col-span-2 hidden">
                    <p class="text-xs text-gray-500 mb-1">Cancellation Reason</p>
                    <p id="result-cancellation-reason" class="text-sm font-semibold text-red-600">-</p>
                </div>
            </div>
        </x-card>
    </main>

    <x-loading_modal id="trackLoading" text="Searching your ticket status..." />

    <script src="{{ asset('assets/js/track.js') }}"></script>
@endsection
