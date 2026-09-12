@extends('layouts.track_submit')

@section('title', 'Track Request')

@section('content')

    <script src="{{ asset('assets/js/track.js') }}"></script>
    <main class="w-[calc(100%-2rem)] max-w-md sm:max-w-none sm:w-fit h-fit px-6 py-6 sm:px-10 sm:py-8 bg-[#020d1d] rounded-xl mx-auto my-4 sm:my-8 shadow-2xl shadow-black/60">

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
            <i class="ti ti-git-pull-request text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-center text-white mt-2">Track Your Ticket</h1>
        <p class="text-center text-xs text-gray-400 mt-2">Enter your reference number to check the status of your ticket.</p>
        
        <div class="flex justify-center items-center mt-2">
            <p class="text-center text-xs text-gray-400">
                Want to submit another ticket?
            </p>

            <a
                href="{{ route('submit.request') }}"
                class="text-xs font-semibold text-[#2c51ec] ml-2"
            >
                Click Here.
            </a>

        </div>

        <form id="trackForm" class="w-full sm:w-[500px] mt-5 flex flex-col gap-2" method="GET">
            <div class="flex flex-row items-center gap-3">
                <x-input
                    id="refInput"
                    label="Reference Number"
                    placeholder="Enter Reference Number"
                    name="ref"
                    leftIcon="ti ti-ticket"
                    backgroundColor="#071827"
                    focusColor="#2c51ec"
                    iconFocusColor="#2c51ec"
                    style="flex: 1;"
                />

                <x-button
                    color="blue"
                    type="submit"
                    class="h-10"
                >
                    Track
                </x-button>
            </div>
            <p id="refError" class="hidden text-xs text-red-400 flex items-center gap-1 ml-1">
                <i class="ti ti-alert-circle"></i>
                Reference number is required
            </p>
        </form>

        <x-card id="resultCard" color="d-blue" label="Request Details" class="mt-6 hidden">
            <x-slot:actions>
                <button onclick="document.getElementById('resultCard').classList.add('hidden')" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </x-slot:actions>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Ticket Reference No.</p>
                    <p class="text-sm font-semibold">REQ-2026-00123</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Date Requested</p>
                    <p class="text-sm font-semibold">Sep 02, 2026</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Category</p>
                    <p class="text-sm font-semibold">Hardware Issue</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Issue</p>
                    <p class="text-sm font-semibold">Printer Not Responding</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Priority</p>
                    <x-badge label="High" color="red" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    <x-badge label="In Progress" color="yellow" icon="ti ti-loader" />
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Details</p>
                    <p class="text-sm font-semibold">Printer not responding on the 2nd floor office.</p>
                </div>
            </div>
        </x-card>
    </main>

    <x-loading_modal id="trackLoading" text="Searching your ticket status..." />

    <script src="{{ asset('assets/js/track.js') }}"></script>
@endsection
