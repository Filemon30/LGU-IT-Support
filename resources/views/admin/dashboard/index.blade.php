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
                        icon="ti ti-clock-hour-5"
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
                                $pKey = strtolower($p['label']);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600">{{ $p['label'] }}</span>
                                    <span id="priority-{{ $pKey }}" class="text-xs font-bold" style="color: {{ $p['color'] }};">{{ $p['count'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div
                                        id="priority-{{ $pKey }}-bar"
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


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
            <x-card
                label="RECENT TICKET REQUEST"
                :fill="false"
            >
                <x-table
                    :columns="['Ticket No.', 'Requester', 'Category', 'Relative Time']"
                    maxHeight="484px"
                    minWidth="500px"
                >
                    <x-slot:body>
                        @forelse($recentTickets as $ticket)
                            <tr id="recent-row-{{ $ticket['ticket_ref_num'] }}">
                                <td class="px-4 py-3 text-gray-900">{{ $ticket['ticket_ref_num'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $ticket['requester_name'] }}</td>
                                <td class="px-4 py-3 text-gray-900">
                                    @php
                                        $catColors = [
                                            'Hardware' => 'orange',
                                            'Software' => 'blue',
                                            'Network' => 'green',
                                            'Others' => 'gray',
                                        ];
                                        $catIcons = [
                                            'Hardware' => 'ti ti-devices-2',
                                            'Software' => 'ti ti-apps',
                                            'Network' => 'ti ti-wifi',
                                            'Others' => 'ti ti-tag',
                                        ];
                                    @endphp
                                    <x-badge
                                        icon="{{ $catIcons[$ticket['category_name']] ?? 'ti ti-tag' }}"
                                        color="{{ $catColors[$ticket['category_name']] ?? 'gray' }}"
                                    >
                                        {{ $ticket['category_name'] }}
                                    </x-badge>
                                </td>
                                
                                <td class="px-4 py-3 text-gray-400">{{ $ticket['created_at']->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400 text-xs">No recent ticket requests</td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-table>
            </x-card>

            <x-card
                label="REQUEST TICKET REASSIGNMENT"
                :fill="false"
            >
                <x-table
                    :columns="['Ticket No.', 'Assigned Staff', 'Relative Time']"
                    maxHeight="484px"
                    minWidth="350px"
                >
                    <x-slot:body>
                        @forelse($reassignmentRequests as $req)
                            <tr id="reassign-row-{{ $req['ticket_ref_num'] }}">
                                <td class="px-4 py-3 text-gray-900">{{ $req['ticket_ref_num'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $req['assignee_name'] }}</td>
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
        function incrementEl(id) {
            var el = document.getElementById(id);
            if (el) el.textContent = parseInt(el.textContent || '0', 10) + 1;
        }

        function decrementEl(id) {
            var el = document.getElementById(id);
            if (el) {
                var current = parseInt(el.textContent || '0', 10);
                if (current > 0) el.textContent = current - 1;
            }
        }

        function recalcPriorityBars() {
            var keys = ['critical', 'high', 'medium', 'low'];
            var total = 0;
            keys.forEach(function (k) {
                var el = document.getElementById('priority-' + k);
                if (el) total += parseInt(el.textContent) || 0;
            });
            keys.forEach(function (k) {
                var countEl = document.getElementById('priority-' + k);
                var barEl = document.getElementById('priority-' + k + '-bar');
                if (countEl && barEl) {
                    var pct = total > 0 ? ((parseInt(countEl.textContent) || 0) / total) * 100 : 0;
                    barEl.style.width = pct + '%';
                }
            });
        }

        function statusBadge(status) {
            var colors = {
                'Pending': { bg: '249,115,22', c: '#fb923c' },
                'Confirmed': { bg: '34,197,94', c: '#4ade80' },
                'On Progress': { bg: '234,179,8', c: '#facc15' },
                'Resolved': { bg: '59,130,246', c: '#60a5fa' },
                'Cancelled': { bg: '239,68,68', c: '#f87171' }
            };
            var icons = {
                'Pending': 'ti ti-clock',
                'Confirmed': 'ti ti-circle-check',
                'On Progress': 'ti ti-loader',
                'Resolved': 'ti ti-circle-check-filled',
                'Cancelled': 'ti ti-circle-x'
            };
            var s = colors[status] || { bg: '107,114,128', c: '#9ca3af' };
            var ic = icons[status] || 'ti ti-help-circle';
            return '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(' + s.bg + ',0.10);color:' + s.c + ';border-color:rgba(' + s.bg + ',0.30)"><i class="' + ic + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + status + '</span></span>';
        }

        function categoryBadge(name) {
            var colors = {
                'Hardware': { bg: '249,115,22', c: '#fb923c', ic: 'ti ti-devices-2' },
                'Software': { bg: '59,130,246', c: '#60a5fa', ic: 'ti ti-apps' },
                'Network': { bg: '34,197,94', c: '#4ade80', ic: 'ti ti-wifi' },
                'Others': { bg: '107,114,128', c: '#9ca3af', ic: 'ti ti-tag' }
            };
            var s = colors[name] || { bg: '107,114,128', c: '#9ca3af', ic: 'ti ti-tag' };
            return '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(' + s.bg + ',0.10);color:' + s.c + ';border-color:rgba(' + s.bg + ',0.30)"><i class="' + s.ic + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + name + '</span></span>';
        }

        // ─── Category Line Chart ───────────────────────────
        (function () {
            var chartEl = document.getElementById('categoryLineChart');
            var chartPayload = document.getElementById('chart-data');
            if (!chartEl || !chartPayload) return;
            chartPayload = JSON.parse(chartPayload.textContent);
            var categoryChart = null;

            function drawCategoryChart() {
                if (typeof Chart === 'undefined' || !chartPayload) return;
                if (categoryChart) categoryChart.destroy();

                categoryChart = new Chart(chartEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: chartPayload.labels,
                        datasets: [
                            { label: 'Hardware', data: chartPayload.hardware, borderColor: '#3b82f6', backgroundColor: 'transparent', tension: 0.35, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#3b82f6', pointBorderColor: '#ffffff', pointBorderWidth: 2 },
                            { label: 'Software', data: chartPayload.software, borderColor: '#eab308', backgroundColor: 'transparent', tension: 0.35, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#eab308', pointBorderColor: '#ffffff', pointBorderWidth: 2 },
                            { label: 'Network', data: chartPayload.network, borderColor: '#a855f7', backgroundColor: 'transparent', tension: 0.35, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5, pointBackgroundColor: '#a855f7', pointBorderColor: '#ffffff', pointBorderWidth: 2 },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 6, boxHeight: 6, padding: 16 } },
                            tooltip: { callbacks: { title: (items) => items[0].label + ' ' + chartPayload.year }, boxPadding: 3 },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
            }

            drawCategoryChart();
        })();

        // ─── Reverb Real-time Listeners ────────────────────
        (function () {
            function bindEcho() {
                if (!window.Echo) return false;

                window.Echo.channel('tickets')
                    .listen('.new-ticket', (e) => {
                        incrementEl('stat-total');
                        incrementEl('stat-pending');
                        incrementEl('stat-unassigned');

                        var pKey = (e.priority_name || '').toLowerCase();
                        if (pKey) { incrementEl('priority-' + pKey); recalcPriorityBars(); }

                        var tableBody = document.querySelector('#recent-row-{{ $recentTickets->first()["ticket_ref_num"] ?? "none" }}')?.closest('tbody');
                        if (tableBody) {
                            var emptyRow = tableBody.querySelector('td[colspan]');
                            if (emptyRow) emptyRow.closest('tr').remove();
                            var tr = document.createElement('tr');
                            tr.id = 'recent-row-' + e.ticket_ref_num;
                            tr.innerHTML = '<td class="px-4 py-3 text-gray-900">' + e.ticket_ref_num + '</td><td class="px-4 py-3 text-gray-900">' + e.requester_name + '</td><td class="px-4 py-3 text-gray-900">' + categoryBadge(e.category_name) + '</td><td class="px-4 py-3 text-gray-400">just now</td>';
                            tableBody.insertBefore(tr, tableBody.firstChild);
                            var rows = tableBody.querySelectorAll('tr');
                            if (rows.length > 11) tableBody.removeChild(rows[rows.length - 1]);
                        }
                    })
                    .listen('.ticket-assigned', (e) => {
                        if (e.ticket_status === 'Confirmed') {
                            decrementEl('stat-pending');
                            incrementEl('stat-confirmed');
                            decrementEl('stat-unassigned');
                        }

                        var reassignTable = document.getElementById('reassign-row-{{ $reassignmentRequests->first()["ticket_ref_num"] ?? "none" }}')?.closest('tbody');
                        if (reassignTable) {
                            var emptyRow = reassignTable.querySelector('td[colspan]');
                            if (emptyRow) emptyRow.closest('tr').remove();
                            var tr = document.createElement('tr');
                            tr.id = 'reassign-row-' + e.ticket_ref_num;
                            tr.innerHTML = '<td class="px-4 py-3 text-gray-900">' + e.ticket_ref_num + '</td><td class="px-4 py-3 text-gray-900">' + e.assigned_name + '</td><td class="px-4 py-3 text-gray-400">just now</td>';
                            reassignTable.insertBefore(tr, reassignTable.firstChild);
                            var rows = reassignTable.querySelectorAll('tr');
                            if (rows.length > 11) reassignTable.removeChild(rows[rows.length - 1]);
                        }
                    })
                    .listen('.ticket-soft-deleted', (e) => {
                        decrementEl('stat-total');
                        if (e.old_status === 'Pending') decrementEl('stat-pending');
                        else if (e.old_status === 'Confirmed') decrementEl('stat-confirmed');
                        else if (e.old_status === 'On Progress') decrementEl('stat-in-progress');
                        else if (e.old_status === 'Cancelled') decrementEl('stat-cancelled');

                        var row = document.getElementById('recent-row-' + e.ticket_ref_num);
                        if (row) row.remove();
                    });

                return true;
            }

            if (!bindEcho()) {
                var tries = 0;
                var timer = setInterval(function () {
                    tries++;
                    if (bindEcho() || tries > 50) clearInterval(timer);
                }, 200);
            }
        })();
    </script>

@endsection
