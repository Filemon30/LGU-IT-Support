<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="reverb-key" content="{{ config('broadcasting.connections.reverb.key') }}">
    <meta name="reverb-host" content="{{ config('broadcasting.connections.reverb.options.host') }}">
    <meta name="reverb-port" content="{{ config('broadcasting.connections.reverb.options.port') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/biringan.png') }}">
    <title>Ticket Dashboard - City of Biringan IT Support</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Header --}}
    <div class="bg-white py-4 shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-3">
            <img src="{{ asset('assets/images/biringan.png') }}" alt="Logo" class="h-8 w-8">
            <h1 class="text-lg font-bold text-gray-900">Realtime Ticket Monitoring</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

        {{-- Cards & Priority Side by Side --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            <x-card class="lg:col-span-2 h-full">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 h-full">

                    <x-overview_card
                        icon="ti ti-ticket"
                        label="Total Tickets"
                        :total="$totalTickets"
                        id="total-tickets"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-refresh-dot"
                        label="Pending"
                        :total="$pendingCount"
                        id="pending-count"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-checklist"
                        label="Confirmed"
                        :total="$confirmedCount"
                        id="confirmed-count"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-loader"
                        label="In Progress"
                        :total="$inProgressCount"
                        id="in-progress-count"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-rosette-discount-check"
                        label="Resolved"
                        :total="$resolvedCount"
                        id="resolved-count"
                        color="d-blue"
                    />

                    <x-overview_card
                        icon="ti ti-ticket-off"
                        label="Unassigned"
                        :total="$unassignedCount"
                        id="unassigned-count"
                        color="d-blue"
                    />

                </div>
            </x-card>

            <x-card class="h-full">
                <x-bar_graph
                    label="Ticket Priorities"
                    :items="[
                        ['label' => 'Critical', 'count' => $criticalCount, 'color' => '#dc2626', 'id' => 'priority-critical'],
                        ['label' => 'High', 'count' => $highCount, 'color' => '#f97316', 'id' => 'priority-high'],
                        ['label' => 'Medium', 'count' => $mediumCount, 'color' => '#eab308', 'id' => 'priority-medium'],
                        ['label' => 'Low', 'count' => $lowCount, 'color' => '#22c55e', 'id' => 'priority-low'],
                    ]"
                />
            </x-card>
        </div>

        {{-- Ticket Types --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <x-card>
                <div class="flex items-center gap-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 0.375rem; background-color: #ffedd5;">
                        <i class="ti ti-device-desktop" style="font-size: 2rem; color: #ea580c;"></i>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">Hardware</p>
                        <p id="hw-total" class="text-3xl font-bold text-gray-900">{{ $hwTotal }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pending</span>
                        <span id="hw-pending" class="font-semibold text-gray-900">{{ $hardwareStatuses['Pending'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Progress</span>
                        <span id="hw-progress" class="font-semibold text-gray-900">{{ $hardwareStatuses['On Progress'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Resolved</span>
                        <span id="hw-resolved" class="font-semibold text-gray-900">{{ $hardwareStatuses['Resolved'] ?? 0 }}</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center gap-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 0.375rem; background-color: #dbeafe;">
                        <i class="ti ti-brand-windows" style="font-size: 2rem; color: #2563eb;"></i>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">Software</p>
                        <p id="sw-total" class="text-3xl font-bold text-gray-900">{{ $swTotal }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pending</span>
                        <span id="sw-pending" class="font-semibold text-gray-900">{{ $softwareStatuses['Pending'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Progress</span>
                        <span id="sw-progress" class="font-semibold text-gray-900">{{ $softwareStatuses['On Progress'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Resolved</span>
                        <span id="sw-resolved" class="font-semibold text-gray-900">{{ $softwareStatuses['Resolved'] ?? 0 }}</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center gap-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 0.375rem; background-color: #dcfce7;">
                        <i class="ti ti-wifi" style="font-size: 2rem; color: #16a34a;"></i>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">Network</p>
                        <p id="net-total" class="text-3xl font-bold text-gray-900">{{ $netTotal }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pending</span>
                        <span id="net-pending" class="font-semibold text-gray-900">{{ $networkStatuses['Pending'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Progress</span>
                        <span id="net-progress" class="font-semibold text-gray-900">{{ $networkStatuses['On Progress'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Resolved</span>
                        <span id="net-resolved" class="font-semibold text-gray-900">{{ $networkStatuses['Resolved'] ?? 0 }}</span>
                    </div>
                </div>
            </x-card>

        </div>

    </div>

    {{-- New Ticket Modal --}}
    <div
        id="new-ticket-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
    >
        <div class="flex w-full max-w-md flex-col items-center rounded-2xl bg-white px-8 py-10 shadow-xl">
            <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 1rem; background-color: #071f45; margin-bottom: 0.5rem;">
                <i class="ti ti-bell" style="font-size: 2rem; color: #FFFFFF;"></i>
            </div>
            <p class="text-gray-600 font-semibold text-xs mb-2"> Live Update </p>
            <p id="new-ticket-message" class="text-lg text-black font-bold text-center mb-4 mt-6"></p>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('assets/js/ticket.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initTicketChannel();
        });
    </script>

</body>
</html>
