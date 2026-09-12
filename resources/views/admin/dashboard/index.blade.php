@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mt-2.5 space-y-6 ">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            <x-card class="lg:col-span-2 h-full">

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 h-full">

                    <x-overview_card
                        icon="ti ti-ticket"
                        label="Tickets"
                        :total="$totalTickets"
                        color="d-blue"
                        id="stat-total"
                    />

                    <x-overview_card
                        icon="ti ti-refresh-dot"
                        label="Pending Tickets"
                        :total="$pendingCount"
                        color="d-blue"
                        id="stat-pending"
                    />

                    <x-overview_card
                        icon="ti ti-checklist"
                        label="Confirmed Tickets"
                        :total="$confirmedCount"
                        color="d-blue"
                        id="stat-confirmed"
                    />

                    <x-overview_card
                        icon="ti ti-loader"
                        label="On Progress Tickets"
                        :total="$inProgressCount"
                        color="d-blue"
                        id="stat-in-progress"
                    />

                    <x-overview_card
                        icon="ti ti-ticket-off"
                        label="Cancelled Tickets"
                        :total="$cancelledCount"
                        color="d-blue"
                        id="stat-cancelled"
                    />

                    <x-overview_card
                        icon="ti ti-rosette-discount-check"
                        label="Unassigned Tickets"
                        :total="$unassignedCount"
                        color="d-blue"
                        id="stat-unassigned"
                    />
                </div>
            </x-card>

            <x-card class="h-full">
                @php
                    $totalPriority = $criticalCount + $highCount + $mediumCount + $lowCount;
                @endphp
                <div class="flex flex-col h-full gap-4">
                    <h3 class="text-sm font-semibold text-gray-700">Ticket Priorities</h3>

                    <div class="flex flex-col justify-between flex-1 gap-4">
                        @php
                            $priorities = [
                                ['label' => 'Critical', 'count' => $criticalCount, 'color' => '#dc2626'],
                                ['label' => 'High', 'count' => $highCount, 'color' => '#f97316'],
                                ['label' => 'Medium', 'count' => $mediumCount, 'color' => '#eab308'],
                                ['label' => 'Low', 'count' => $lowCount, 'color' => '#22c55e'],
                            ];
                        @endphp
                        @foreach($priorities as $p)
                            @php
                                $pct = $totalPriority > 0 ? ($p['count'] / $totalPriority) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">{{ $p['label'] }}</span>
                                    <span class="text-xs font-bold" style="color: {{ $p['color'] }};">{{ $p['count'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div
                                        class="h-3 rounded-full transition-all duration-500"
                                        style="width: {{ $pct }}%; background-color: {{ $p['color'] }};"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
        </div>



        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2">
                <livewire:monthly-ticket-request />
            </div>

            <div>
                <livewire:weekly-ticket-request />
            </div>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card
                label="RECENT TICKET REQUEST"
            >
                <x-table
                    :columns="['Ticket No.', 'From', 'Category', 'Status', 'Relative Time']"
                    maxHeight="484px"
                >
                    <x-slot:body>
                        @forelse($recentTickets as $ticket)
                            <tr id="recent-row-{{ $ticket['ticket_ref_num'] }}">
                                <td class="px-4 py-3 text-gray-900">{{ $ticket['ticket_ref_num'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $ticket['requester_name'] }}</td>
                                <td class="px-4 py-3 text-gray-900">
                                    @php
                                        $catColors = [
                                            'Hardware' => 'blue',
                                            'Software' => 'yellow',
                                            'Network' => 'purple',
                                        ];
                                        $catIcons = [
                                            'Hardware' => 'ti ti-devices-2',
                                            'Software' => 'ti ti-apps',
                                            'Network' => 'ti ti-wifi',
                                        ];
                                    @endphp
                                    <x-badge
                                        icon="{{ $catIcons[$ticket['category_name']] ?? 'ti ti-tag' }}"
                                        color="{{ $catColors[$ticket['category_name']] ?? 'gray' }}"
                                    >
                                        {{ $ticket['category_name'] }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-gray-900">
                                    @php
                                        $statusColors = [
                                            'Pending' => 'orange',
                                            'Confirmed' => 'green',
                                            'On Progress' => 'cyan',
                                            'Resolved' => 'green',
                                            'Cancelled' => 'red',
                                        ];
                                        $statusIcons = [
                                            'Pending' => 'ti ti-clock',
                                            'Confirmed' => 'ti ti-circle-check',
                                            'On Progress' => 'ti ti-loader',
                                            'Resolved' => 'ti ti-circle-check-filled',
                                            'Cancelled' => 'ti ti-circle-x',
                                        ];
                                    @endphp
                                    <x-badge
                                        icon="{{ $statusIcons[$ticket['ticket_status']] ?? 'ti ti-help-circle' }}"
                                        label="{{ $ticket['ticket_status'] }}"
                                        color="{{ $statusColors[$ticket['ticket_status']] ?? 'gray' }}"
                                    />
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $ticket['created_at']->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No tickets yet</td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-table>
            </x-card>

            <x-card
                label="REQUEST TICKET REASSIGNMENT"
            >
                <x-table
                    :columns="['Ticket No.', 'Assigned Staff', 'Status', 'Relative Time']"
                    maxHeight="484px"
                >
                    <x-slot:body>
                        @forelse($reassignmentRequests as $req)
                            <tr id="reassign-row-{{ $req['ticket_ref_num'] }}">
                                <td class="px-4 py-3 text-gray-900">{{ $req['ticket_ref_num'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $req['assignee_name'] }}</td>
                                <td class="px-4 py-3 text-gray-900">
                                    @php
                                        $statusColors = [
                                            'Pending' => 'orange',
                                            'Confirmed' => 'green',
                                            'On Progress' => 'cyan',
                                            'Resolved' => 'green',
                                            'Cancelled' => 'red',
                                        ];
                                        $statusIcons = [
                                            'Pending' => 'ti ti-clock',
                                            'Confirmed' => 'ti ti-circle-check',
                                            'On Progress' => 'ti ti-loader',
                                            'Resolved' => 'ti ti-circle-check-filled',
                                            'Cancelled' => 'ti ti-circle-x',
                                        ];
                                    @endphp
                                    <x-badge
                                        icon="{{ $statusIcons[$req['ticket_status']] ?? 'ti ti-help-circle' }}"
                                        label="{{ $req['ticket_status'] }}"
                                        color="{{ $statusColors[$req['ticket_status']] ?? 'gray' }}"
                                    />
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $req['created_at']->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No reassignment requests</td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-table>
            </x-card>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    @endassets

    <script type="application/json" id="chart-data">
        @json($chartData)
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ─── Category Line Chart ───────────────────────────
            const chartEl = document.getElementById('categoryLineChart');
            const chartPayload = JSON.parse(document.getElementById('chart-data').textContent);

            let categoryChart = null;

            function drawCategoryChart() {
                if (typeof Chart === 'undefined' || !chartPayload) return;

                if (categoryChart) categoryChart.destroy();

                categoryChart = new Chart(chartEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: chartPayload.labels,
                        datasets: [
                            {
                                label: 'Hardware',
                                data: chartPayload.hardware,
                                borderColor: '#3b82f6',
                                backgroundColor: 'transparent',
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                            },
                            {
                                label: 'Software',
                                data: chartPayload.software,
                                borderColor: '#eab308',
                                backgroundColor: 'transparent',
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#eab308',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                            },
                            {
                                label: 'Network',
                                data: chartPayload.network,
                                borderColor: '#a855f7',
                                backgroundColor: 'transparent',
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#a855f7',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 6,
                                    boxHeight: 6,
                                    padding: 16,
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    title: (items) => items[0].label + ' ' + chartPayload.year,
                                },
                                boxPadding: 3,
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                            },
                        },
                    },
                });
            }

            drawCategoryChart();

            // ─── Helper: Status badge HTML ─────────────────────
            function statusBadge(status) {
                const colors = {
                    'Pending': 'orange',
                    'Confirmed': 'green',
                    'On Progress': 'cyan',
                    'Resolved': 'green',
                    'Cancelled': 'red',
                };
                const icons = {
                    'Pending': 'ti ti-clock',
                    'Confirmed': 'ti ti-circle-check',
                    'On Progress': 'ti ti-loader',
                    'Resolved': 'ti ti-circle-check-filled',
                    'Cancelled': 'ti ti-circle-x',
                };
                const c = colors[status] || 'gray';
                const ic = icons[status] || 'ti ti-help-circle';
                return `<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(${c === 'orange' ? '249,115,22' : c === 'green' ? '34,197,94' : c === 'cyan' ? '6,182,212' : c === 'red' ? '239,68,68' : '107,114,128'},0.10);color:${c === 'orange' ? '#fb923c' : c === 'green' ? '#4ade80' : c === 'cyan' ? '#22d3ee' : c === 'red' ? '#f87171' : '#9ca3af'};border-color:rgba(${c === 'orange' ? '249,115,22' : c === 'green' ? '34,197,94' : c === 'cyan' ? '6,182,212' : c === 'red' ? '239,68,68' : '107,114,128'},0.30)"><i class="${ic} text-[0.7rem]"></i><span class="text-[0.7rem]">${status}</span></span>`;
            }

            function categoryBadge(name) {
                const colors = { 'Hardware': { bg: 'rgba(59,130,246,0.10)', c: '#60a5fa', bc: 'rgba(59,130,246,0.30)', ic: 'ti ti-devices-2' }, 'Software': { bg: 'rgba(234,179,8,0.10)', c: '#facc15', bc: 'rgba(234,179,8,0.30)', ic: 'ti ti-apps' }, 'Network': { bg: 'rgba(168,85,247,0.10)', c: '#c084fc', bc: 'rgba(168,85,247,0.30)', ic: 'ti ti-wifi' } };
                const s = colors[name] || { bg: 'rgba(107,114,128,0.10)', c: '#9ca3af', bc: 'rgba(107,114,128,0.30)', ic: 'ti ti-tag' };
                return `<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:${s.bg};color:${s.c};border-color:${s.bc}"><i class="${s.ic} text-[0.7rem]"></i><span class="text-[0.7rem]">${name}</span></span>`;
            }

            // ─── Reverb Real-time Listeners ────────────────────
            if (window.Echo) {

                window.Echo.channel('tickets')
                    .listen('.new-ticket', (e) => {
                        // Increment counters
                        const totalEl = document.getElementById('stat-total');
                        const pendingEl = document.getElementById('stat-pending');
                        const unassignedEl = document.getElementById('stat-unassigned');

                        if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + 1;
                        if (pendingEl) pendingEl.textContent = parseInt(pendingEl.textContent) + 1;
                        if (e.ticket_status !== 'Cancelled' && e.ticket_status !== 'Resolved') {
                            if (unassignedEl) unassignedEl.textContent = parseInt(unassignedEl.textContent) + 1;
                        }

                        // Prepend row to recent tickets table
                        const tableBody = document.querySelector('#recent-row-{{ $recentTickets->first()["ticket_ref_num"] ?? "none" }}')?.closest('tbody');
                        if (tableBody) {
                            const emptyRow = tableBody.querySelector('td[colspan]');
                            if (emptyRow) emptyRow.closest('tr').remove();

                            const tr = document.createElement('tr');
                            tr.id = 'recent-row-' + e.ticket_ref_num;
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-gray-900">${e.ticket_ref_num}</td>
                                <td class="px-4 py-3 text-gray-900">${e.requester_name}</td>
                                <td class="px-4 py-3 text-gray-900">${categoryBadge(e.category_name)}</td>
                                <td class="px-4 py-3 text-gray-900">${statusBadge(e.ticket_status)}</td>
                                <td class="px-4 py-3 text-gray-400">just now</td>
                            `;
                            tableBody.insertBefore(tr, tableBody.firstChild);

                            // Remove last row if more than 11
                            const rows = tableBody.querySelectorAll('tr');
                            if (rows.length > 11) {
                                tableBody.removeChild(rows[rows.length - 1]);
                            }
                        }
                    })
                    .listen('.ticket-assigned', (e) => {
                        const pendingEl = document.getElementById('stat-pending');
                        const confirmedEl = document.getElementById('stat-confirmed');
                        const unassignedEl = document.getElementById('stat-unassigned');

                        if (e.ticket_status === 'Confirmed') {
                            if (pendingEl) pendingEl.textContent = Math.max(0, parseInt(pendingEl.textContent) - 1);
                            if (confirmedEl) confirmedEl.textContent = parseInt(confirmedEl.textContent) + 1;
                            if (unassignedEl) unassignedEl.textContent = Math.max(0, parseInt(unassignedEl.textContent) - 1);
                        }

                        // Prepend row to reassignment table
                        const reassignTable = document.getElementById('reassign-row-{{ $reassignmentRequests->first()["ticket_ref_num"] ?? "none" }}')?.closest('tbody');
                        if (reassignTable) {
                            const emptyRow = reassignTable.querySelector('td[colspan]');
                            if (emptyRow) emptyRow.closest('tr').remove();

                            const tr = document.createElement('tr');
                            tr.id = 'reassign-row-' + e.ticket_ref_num;
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-gray-900">${e.ticket_ref_num}</td>
                                <td class="px-4 py-3 text-gray-900">${e.assigned_name}</td>
                                <td class="px-4 py-3 text-gray-900">${statusBadge(e.ticket_status)}</td>
                                <td class="px-4 py-3 text-gray-400">just now</td>
                            `;
                            reassignTable.insertBefore(tr, reassignTable.firstChild);

                            const rows = reassignTable.querySelectorAll('tr');
                            if (rows.length > 11) {
                                reassignTable.removeChild(rows[rows.length - 1]);
                            }
                        }
                    });
            }
        });
    </script>

@endsection
