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
    <title>Ticket Dashboard - City of Biringan EnchantaTech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        icon="ti ti-clock-hour-5"
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <x-card>
                <div class="flex items-center gap-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 0.375rem; background-color: #ffedd5;">
                        <i class="ti ti-devices-2" style="font-size: 2rem; color: #ea580c;"></i>
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
                        <span class="text-gray-500">Confirmed</span>
                        <span id="hw-confirmed" class="font-semibold text-gray-900">{{ $hardwareStatuses['Confirmed'] ?? 0 }}</span>
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
                        <span class="text-gray-500">Confirmed</span>
                        <span id="sw-confirmed" class="font-semibold text-gray-900">{{ $softwareStatuses['Confirmed'] ?? 0 }}</span>
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
                        <span class="text-gray-500">Confirmed</span>
                        <span id="net-confirmed" class="font-semibold text-gray-900">{{ $networkStatuses['Confirmed'] ?? 0 }}</span>
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

            <x-card>
                <div class="flex items-center gap-4 mb-4">
                    <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 0.375rem; background-color: #fef9c3;">
                        <i class="ti ti-folder" style="font-size: 2rem; color: #eab308;"></i>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">Others</p>
                        <p id="oth-total" class="text-3xl font-bold text-gray-900">{{ $othersTotal }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Pending</span>
                        <span id="oth-pending" class="font-semibold text-gray-900">{{ $othersStatuses['Pending'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Confirmed</span>
                        <span id="oth-confirmed" class="font-semibold text-gray-900">{{ $othersStatuses['Confirmed'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Progress</span>
                        <span id="oth-progress" class="font-semibold text-gray-900">{{ $othersStatuses['On Progress'] ?? 0 }}</span>
                    </div>
                    <div class="rounded-md bg-gray-50 px-3 py-2 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Resolved</span>
                        <span id="oth-resolved" class="font-semibold text-gray-900">{{ $othersStatuses['Resolved'] ?? 0 }}</span>
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

    <script>
        /*
        |--------------------------------------------------------------------------
        | Utility Helpers
        |--------------------------------------------------------------------------
        */

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

        function getCategoryPrefix(categoryName) {
            var map = { 'Hardware': 'hw', 'Software': 'sw', 'Network': 'net', 'Others': 'oth' };
            return map[categoryName] || null;
        }

        function incrementStatusForCategory(prefix, status) {
            if (status === 'Pending') incrementEl(prefix + '-pending');
            else if (status === 'Confirmed') incrementEl(prefix + '-confirmed');
            else if (status === 'On Progress') incrementEl(prefix + '-progress');
            else if (status === 'Resolved') incrementEl(prefix + '-resolved');
        }

        function decrementStatusForCategory(prefix, status) {
            if (status === 'Pending') decrementEl(prefix + '-pending');
            else if (status === 'Confirmed') decrementEl(prefix + '-confirmed');
            else if (status === 'On Progress') decrementEl(prefix + '-progress');
            else if (status === 'Resolved') decrementEl(prefix + '-resolved');
        }


        /*
        |--------------------------------------------------------------------------
        | Modal
        |--------------------------------------------------------------------------
        */

        function openModal(modalId) {
            var modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }


        /*
        |--------------------------------------------------------------------------
        | Notification Sound
        |--------------------------------------------------------------------------
        */

        function playNotificationSound(callback) {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                var notes = [523.25, 659.25, 783.99];
                var delay = 0;

                notes.forEach(function (freq) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.value = freq;

                    gain.gain.setValueAtTime(0.3, ctx.currentTime + delay);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + delay + 0.4);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + delay);
                    osc.stop(ctx.currentTime + delay + 0.4);

                    delay += 0.15;
                });

                if (callback) setTimeout(callback, 2000);
            } catch (e) {
                if (callback) callback();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Text-to-Speech
        |--------------------------------------------------------------------------
        */

        function speak(text, onEnd) {
            if (!('speechSynthesis' in window)) {
                if (onEnd) onEnd();
                return;
            }
            window.speechSynthesis.cancel();
            var u = new SpeechSynthesisUtterance(text);
            u.rate = 0.85;
            u.pitch = 1.2;
            u.volume = 5;

            if (onEnd) u.onend = onEnd;

            var voices = window.speechSynthesis.getVoices();

            if (voices.length === 0) {
                window.speechSynthesis.onvoiceschanged = function () {
                    voices = window.speechSynthesis.getVoices();
                    setFemaleVoice(u, voices);
                    window.speechSynthesis.speak(u);
                };
            } else {
                setFemaleVoice(u, voices);
                window.speechSynthesis.speak(u);
            }
        }

        function setFemaleVoice(utterance, voices) {
            var female = voices.find(function (v) {
                return v.name.toLowerCase().includes('female') ||
                       v.name.toLowerCase().includes('samantha') ||
                       v.name.toLowerCase().includes('zira') ||
                       v.name.toLowerCase().includes('hazel') ||
                       v.name.toLowerCase().includes('karen') ||
                       v.name.toLowerCase().includes('moira') ||
                       v.name.toLowerCase().includes('tessa') ||
                       v.name.toLowerCase().includes('microsoft zira');
            });
            if (female) utterance.voice = female;
        }


        /*
        |--------------------------------------------------------------------------
        | Build Message
        |--------------------------------------------------------------------------
        */

        function buildTicketMessage(data, forSpeech) {
            var requester = data.requester_name || 'unknown';
            var type = data.requester_type || '';
            var category = data.category_name || '';
            var priority = data.priority_name || '';
            var label = forSpeech ? 'Baranggay' : 'Barangay';

            var sep1 = forSpeech ? ', ' : ' ';
            var sep2 = forSpeech ? ', ' : ' ';
            var msg = 'A new ticket submitted for' + sep1 + (category || 'unknown') + sep2 + 'from ';

            if (type === 'Office Division') {
                msg += requester + ' Division.';
            } else {
                msg += label + ' ' + requester + '.';
            }

            if (priority) {
                if (forSpeech) {
                    msg += ' Priority, ' + priority + '.';
                } else {
                    msg += '<br><p class="font-regular text-gray-600">Priority: ' + priority + '</p>';
                }
            }

            return msg;
        }


        /*
        |--------------------------------------------------------------------------
        | Dashboard Counter Updates
        |--------------------------------------------------------------------------
        */

        function updateDashboardCounters(data) {
            incrementEl('total-tickets');
            incrementEl('pending-count');
            incrementEl('unassigned-count');

            var priority = (data.priority_name || '').toLowerCase();
            if (priority) {
                var pEl = document.getElementById('priority-' + priority);
                if (pEl) pEl.textContent = parseInt(pEl.textContent || '0', 10) + 1;
                recalcPriorityBars();
            }

            var prefix = getCategoryPrefix(data.category_name);
            if (prefix) {
                incrementEl(prefix + '-total');
                incrementStatusForCategory(prefix, data.ticket_status || 'Pending');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Ticket Queue — processes 1 modal at a time
        |--------------------------------------------------------------------------
        */

        var ticketQueue = [];
        var ticketProcessing = false;

        function processTicketQueue() {
            if (ticketProcessing || ticketQueue.length === 0) return;

            ticketProcessing = true;
            var data = ticketQueue.shift();
            var displayMessage = buildTicketMessage(data, false);
            var speechMessage = buildTicketMessage(data, true);

            document.getElementById('new-ticket-message').innerHTML = displayMessage;

            openModal('new-ticket-modal');

            playNotificationSound(function () {
                speak(speechMessage, function () {
                    closeModal('new-ticket-modal');
                    ticketProcessing = false;
                    processTicketQueue();
                });
            });
        }

        function queueTicket(data) {
            ticketQueue.push(data);
            processTicketQueue();
            updateDashboardCounters(data);
        }


        /*
        |--------------------------------------------------------------------------
        | Reverb WebSocket via Echo
        |--------------------------------------------------------------------------
        */

        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                window.Echo.channel('tickets')
                    .listen('.new-ticket', (e) => {
                        queueTicket(e);
                    })
                    .listen('.ticket-assigned', (e) => {
                        if (e.ticket_status === 'Confirmed') {
                            decrementEl('pending-count');
                            incrementEl('confirmed-count');
                            decrementEl('unassigned-count');
                        }
                    })
                    .listen('.ticket-soft-deleted', (e) => {
                        decrementEl('total-tickets');

                        if (e.old_status === 'Pending') decrementEl('pending-count');
                        else if (e.old_status === 'Confirmed') decrementEl('confirmed-count');
                        else if (e.old_status === 'On Progress') decrementEl('in-progress-count');
                        else if (e.old_status === 'Resolved') decrementEl('resolved-count');

                        var prefix = getCategoryPrefix(e.category_name);
                        if (prefix) {
                            decrementEl(prefix + '-total');
                            decrementStatusForCategory(prefix, e.old_status);
                        }
                    });
            }
        });
    </script>

</body>
</html>
