@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            <div class="lg:col-span-2 h-full">

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 h-full">

                    <x-overview_card
                        icon="ti ti-ticket"
                        label="My Tickets"
                        color="d-blue"
                        id="stat-total"
                    />

                    <x-overview_card
                        icon="ti ti-alert-triangle"
                        label="Critical Priorities"
                        color="red"
                        id="stat-pending"
                    />

                    <x-overview_card
                        icon="ti ti-arrow-up"
                        label="High Priorities"
                        color="orange"
                        id="stat-confirmed"
                    />

                    <x-overview_card
                        icon="ti ti-circle-half-vertical"
                        label="Medium Priorities"
                        color="yellow"
                        id="stat-in-progress"
                    />

                    <x-overview_card
                        icon="ti ti-arrow-down"
                        label="Low Priorities"
                        color="green"
                        id="stat-cancelled"
                    />

                    <x-overview_card
                        icon="ti ti-rosette-discount-check"
                        label="Resolved Tickets"
                        color="blue"
                        id="stat-unassigned"
                    />
                </div>
            </div>

            <x-card class="h-full">
                
            </x-card>
        </div>
    </div>
@endsection
