@extends('layouts.app')

@section('title', 'Tickets')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="reverb-key" content="{{ config('broadcasting.connections.reverb.key') }}">
    <meta name="reverb-host" content="{{ config('broadcasting.connections.reverb.options.host') }}">
    <meta name="reverb-port" content="{{ config('broadcasting.connections.reverb.options.port') }}">
@endsection

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>

    <div class="space-y-6">
         
        <x-card>

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

                </div>

                <x-button
                    color="d-blue"
                    icon="ti ti-external-link"
                    href="{{ route('admin.tickets.dashboard') }}"
                    target="_blank"
                >
                    Open Ticket Dashboard
                </x-button>

            </div>

        </x-card>

        {{-- Staff Table --}}
        <x-card>

            <x-table
                :columns="[
                    'Reference',
                    'Requester',
                    'Priority Level',
                    'Status',
                    'Assigned Status',
                    'Date Submitted'
                ]"
                :actions="true"
            >

                <x-slot:body>
                    @forelse ($tickets as $ticket)
                        @php
                            $priorityColor = match(strtolower($ticket->priority->priority_name ?? '')) {
                                'critical' => 'red',
                                'high' => 'orange',
                                'medium' => 'yellow',
                                'low' => 'green',
                                default => 'gray',
                            };

                            $statusColor = match($ticket->ticket_status) {
                                'Pending' => 'yellow',
                                'Confirmed' => 'blue',
                                'On Progress' => 'cyan',
                                'Resolved' => 'green',
                                'Cancelled' => 'red',
                                default => 'gray',
                            };

                            $statusIcon = match($ticket->ticket_status) {
                                'Pending' => 'ti ti-clock',
                                'Confirmed' => 'ti ti-circle-check',
                                'On Progress' => 'ti ti-loader',
                                'Resolved' => 'ti ti-circle-check-filled',
                                'Cancelled' => 'ti ti-circle-x',
                                default => 'ti ti-help-circle',
                            };

                            $isAssigned = $ticket->assigned_to !== null;
                        @endphp
                        <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                {{ $ticket->ticket_ref_num }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                @if($ticket->requester->requester_type === 'Barangay')
                                    {{ $ticket->requester->barangay->barangay_name ?? '-' }}
                                @else
                                    {{ $ticket->requester->division->division_name ?? '-' }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <x-badge :label="$ticket->priority->priority_name ?? '-'" :color="$priorityColor" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <x-badge :label="$ticket->ticket_status" :color="$statusColor" :icon="$statusIcon" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                @if($isAssigned)
                                    <x-badge :label="$ticket->assignee->information->first_name . ' ' . $ticket->assignee->information->last_name" color="green" icon="ti ti-user" />
                                @else
                                    <x-badge label="Unassigned" color="gray" icon="ti ti-user-off" />
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                                {{ $ticket->created_at->setTimezone('Asia/Manila')->format('m/d/Y h:i A') }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out"
                                        title="Update Ticket"
                                        data-modal-open="update-ticket-modal"
                                        data-ticket-id="{{ $ticket->ticket_id }}"
                                        data-ticket-ref="{{ $ticket->ticket_ref_num }}"
                                        data-requester="{{ $ticket->requester->requester_type === 'Barangay' ? ($ticket->requester->barangay->barangay_name ?? '-') : ($ticket->requester->division->division_name ?? '-') }}"
                                        data-priority="{{ $ticket->priority->priority_name ?? '-' }}"
                                        data-status="{{ $ticket->ticket_status }}"
                                        data-description="{{ $ticket->description ?? '-' }}"
                                        data-assigned="{{ $isAssigned ? $ticket->assignee->information->first_name . ' ' . $ticket->assignee->information->last_name : 'Unassigned' }}"
                                        data-assigned-id="{{ $ticket->assigned_to ?? '' }}"
                                        data-date="{{ $ticket->created_at->setTimezone('Asia/Manila')->format('m/d/Y h:i A') }}"
                                    >
                                        <i class="ti ti-refresh text-xs text-white"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No tickets found.</td>
                        </tr>
                    @endforelse
                </x-slot:body>

            </x-table>

        </x-card>

    </div>

    {{-- Update Ticket Modal --}}
    <x-modal_form
        id="update-ticket-modal"
        title="Ticket Information"
        icon="ti ti-ticket"
        width="max-w-lg"
    >
        <x-card color="dark" class="w-full p-4">
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Reference:</span>
                <span id="modal-ticket-ref" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Requester:</span>
                <span id="modal-requester" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Description:</span>
                <span id="modal-description" class="text-xs font-semibold text-gray-900 text-right max-w-[70%]"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Priority:</span>
                <span id="modal-priority" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Status:</span>
                <span id="modal-status"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Assigned To:</span>
                <span id="modal-assigned" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2">
                <span class="text-xs text-gray-500">Date Submitted:</span>
                <span id="modal-date" class="text-xs font-semibold text-gray-900"></span>
            </div>
        </x-card>

        <div class="flex justify-end mt-4">
            <x-button
                type="button"
                color="outline-green"
                icon="ti ti-user-plus"
                class="assign-btn"
            >
                Assign Ticket
            </x-button>
        </div>
    </x-modal_form>

    {{-- Re-assign Confirmation Modal --}}
    <x-modal_form
        id="reassign-confirm-modal"
        title="Re-assign Ticket"
        icon="ti ti-alert-triangle"
        width="max-w-sm"
    >
        <div class="text-center">
            <p class="text-sm text-gray-600 mb-6">This ticket is already assigned. Do you want to re-assign this ticket?</p>
            <div class="flex justify-center gap-3">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="reassign-confirm-modal"
                >
                    No
                </x-button>
                <x-button
                    type="button"
                    color="outline-green"
                    onclick="proceedToAssign()"
                >
                    Yes, Re-assign
                </x-button>
            </div>
        </div>
    </x-modal_form>

    {{-- Assign Ticket Modal --}}
    <x-modal_form
        id="assign-ticket-modal"
        title="Assign Ticket"
        icon="ti ti-user-plus"
        width="max-w-sm"
    >
        <form onsubmit="return false;" id="assign-ticket-form">
            <input type="hidden" id="assign-ticket-id">

            <div class="mb-4">
                <x-dropdown
                    name="assigned_to"
                    placeholder="Select Staff"
                    size="md"
                    label="Select Staff"
                    :options="$staff->mapWithKeys(function($member) {
                        return [$member->user_id => $member->information->last_name . ', ' . $member->information->first_name . ' ' . ($member->information->middle_name ? $member->information->middle_name[0].'.' : '')];
                    })->toArray()"
                />
                <p id="assign-staff-error" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div class="flex w-full items-center justify-end gap-3">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="assign-ticket-modal"
                >
                    Cancel
                </x-button>

                <x-button
                    type="button"
                    color="outline-green"
                    onclick="submitAssignTicket()"
                >
                    Assign
                </x-button>
            </div>
        </form>
    </x-modal_form>

    <x-loading_modal id="assign-loading" text="Assigning ticket..." />
    <x-success_modal id="assign-success" text="Ticket assigned successfully!" />

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pusherKey = document.querySelector('meta[name="reverb-key"]').getAttribute('content');
            var pusherHost = document.querySelector('meta[name="reverb-host"]').getAttribute('content');
            var pusherPort = document.querySelector('meta[name="reverb-port"]').getAttribute('content');

            var pusher = new Pusher(pusherKey, {
                cluster: 'mt1',
                wsHost: pusherHost,
                wsPort: parseInt(pusherPort),
                wssPort: parseInt(pusherPort),
                forceTLS: false,
                enabledTransports: ['ws'],
                disableStats: true,
            });

            var channel = pusher.subscribe('tickets');

            channel.bind('new-ticket', function (data) {
                var priorityColors = {
                    'critical': 'red',
                    'high': 'orange',
                    'medium': 'yellow',
                    'low': 'green'
                };

                var statusColors = {
                    'Pending': 'yellow',
                    'Confirmed': 'blue',
                    'On Progress': 'cyan',
                    'Resolved': 'green',
                    'Cancelled': 'red'
                };

                var statusIcons = {
                    'Pending': 'ti ti-clock',
                    'Confirmed': 'ti ti-circle-check',
                    'On Progress': 'ti ti-loader',
                    'Resolved': 'ti ti-circle-check-filled',
                    'Cancelled': 'ti ti-circle-x'
                };

                var priorityColor = priorityColors[(data.priority_name || '').toLowerCase()] || 'gray';
                var statusColor = statusColors[data.ticket_status] || 'gray';
                var statusIcon = statusIcons[data.ticket_status] || 'ti ti-help-circle';

                var requesterName = data.requester_name || '-';
                var assignedHtml = '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(107, 114, 128, 0.10); color: #9ca3af; border-color: rgba(107, 114, 128, 0.30);"><i class="ti ti-user-off text-[0.7rem]"></i><span class="text-[0.7rem]">Unassigned</span></span>';

                var date = new Date(data.created_at);
                var options = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', hour12: true };
                var timeStr = date.toLocaleTimeString('en-US', options);
                var dateStr = (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getDate().toString().padStart(2, '0') + '/' + date.getFullYear();
                var formattedDate = dateStr + ' ' + timeStr;

                var row = '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">' +
                    '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + data.ticket_ref_num + '</td>' +
                    '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + requesterName + '</td>' +
                    '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' + (priorityColor === 'red' ? '239, 68, 68' : priorityColor === 'orange' ? '249, 115, 22' : priorityColor === 'yellow' ? '234, 179, 8' : priorityColor === 'green' ? '34, 197, 94' : '107, 114, 128') + ', 0.10); color: ' + (priorityColor === 'red' ? '#f87171' : priorityColor === 'orange' ? '#fb923c' : priorityColor === 'yellow' ? '#facc15' : priorityColor === 'green' ? '#4ade80' : '#9ca3af') + '; border-color: rgba(' + (priorityColor === 'red' ? '239, 68, 68' : priorityColor === 'orange' ? '249, 115, 22' : priorityColor === 'yellow' ? '234, 179, 8' : priorityColor === 'green' ? '34, 197, 94' : '107, 114, 128') + ', 0.30);"><span class="text-[0.7rem]">' + (data.priority_name || '-') + '</span></span></td>' +
                    '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' + (statusColor === 'yellow' ? '234, 179, 8' : statusColor === 'blue' ? '59, 130, 246' : statusColor === 'cyan' ? '6, 182, 212' : statusColor === 'green' ? '34, 197, 94' : statusColor === 'red' ? '239, 68, 68' : '107, 114, 128') + ', 0.10); color: ' + (statusColor === 'yellow' ? '#facc15' : statusColor === 'blue' ? '#60a5fa' : statusColor === 'cyan' ? '#22d3ee' : statusColor === 'green' ? '#4ade80' : statusColor === 'red' ? '#f87171' : '#9ca3af') + '; border-color: rgba(' + (statusColor === 'yellow' ? '234, 179, 8' : statusColor === 'blue' ? '59, 130, 246' : statusColor === 'cyan' ? '6, 182, 212' : statusColor === 'green' ? '34, 197, 94' : statusColor === 'red' ? '239, 68, 68' : '107, 114, 128') + ', 0.30);"><i class="' + statusIcon + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + data.ticket_status + '</span></span></td>' +
                    '<td class="whitespace-nowrap px-4 py-3">' + assignedHtml + '</td>' +
                    '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + formattedDate + '</td>' +
                    '<td class="px-4 py-3"><div class="flex items-center gap-1.5"><button type="button" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Ticket"><i class="ti ti-refresh text-xs text-white"></i></button></div></td>' +
                    '</tr>';

                var tbody = document.querySelector('table tbody');
                var emptyRow = tbody.querySelector('td[colspan="6"]');
                if (emptyRow) {
                    tbody.innerHTML = row;
                } else {
                    tbody.insertAdjacentHTML('beforeend', row);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('click', function(event) {
            var btn = event.target.closest('[data-modal-open="update-ticket-modal"]');
            if (btn) {
                var statusColors = {
                    'Pending': 'yellow',
                    'Confirmed': 'blue',
                    'On Progress': 'cyan',
                    'Resolved': 'green',
                    'Cancelled': 'red'
                };
                var statusIcons = {
                    'Pending': 'ti ti-clock',
                    'Confirmed': 'ti ti-circle-check',
                    'On Progress': 'ti ti-loader',
                    'Resolved': 'ti ti-circle-check-filled',
                    'Cancelled': 'ti ti-circle-x'
                };

                var status = btn.getAttribute('data-status') || '-';
                var statusColor = statusColors[status] || 'gray';
                var statusIcon = statusIcons[status] || 'ti ti-help-circle';

                document.getElementById('modal-ticket-ref').textContent = btn.getAttribute('data-ticket-ref') || '-';
                document.getElementById('modal-requester').textContent = btn.getAttribute('data-requester') || '-';
                document.getElementById('modal-description').textContent = btn.getAttribute('data-description') || '-';
                document.getElementById('modal-priority').textContent = btn.getAttribute('data-priority') || '-';
                document.getElementById('modal-status').innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' + (statusColor === 'yellow' ? '234, 179, 8' : statusColor === 'blue' ? '59, 130, 246' : statusColor === 'cyan' ? '6, 182, 212' : statusColor === 'green' ? '34, 197, 94' : statusColor === 'red' ? '239, 68, 68' : '107, 114, 128') + ', 0.10); color: ' + (statusColor === 'yellow' ? '#facc15' : statusColor === 'blue' ? '#60a5fa' : statusColor === 'cyan' ? '#22d3ee' : statusColor === 'green' ? '#4ade80' : statusColor === 'red' ? '#f87171' : '#9ca3af') + '; border-color: rgba(' + (statusColor === 'yellow' ? '234, 179, 8' : statusColor === 'blue' ? '59, 130, 246' : statusColor === 'cyan' ? '6, 182, 212' : statusColor === 'green' ? '34, 197, 94' : statusColor === 'red' ? '239, 68, 68' : '107, 114, 128') + ', 0.30);"><i class="' + statusIcon + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + status + '</span></span>';
                document.getElementById('modal-assigned').textContent = btn.getAttribute('data-assigned') || '-';
                document.getElementById('modal-date').textContent = btn.getAttribute('data-date') || '-';
                document.getElementById('assign-ticket-id').value = btn.getAttribute('data-ticket-id') || '';
                window.currentTicketAssigned = btn.getAttribute('data-assigned') !== 'Unassigned';
                window.currentAssignedStaffId = btn.getAttribute('data-assigned-id') || '';
            }
        });

        document.addEventListener('click', function(event) {
            var assignBtn = event.target.closest('.assign-btn');
            if (assignBtn) {
                event.preventDefault();
                event.stopPropagation();
                closeModal('update-ticket-modal');
                if (window.currentTicketAssigned) {
                    openModal('reassign-confirm-modal');
                } else {
                    openModal('assign-ticket-modal');
                }
            }
        });

        function proceedToAssign() {
            closeModal('reassign-confirm-modal');
            document.getElementById('assign-staff-error').classList.add('hidden');
            var dropdown = document.querySelector('[name="assigned_to"]');
            if (dropdown) {
                var root = dropdown.closest('details[data-dropdown]');
                if (root) {
                    var placeholderOption = root.querySelector('[data-dropdown-option][data-value=""]');
                    if (placeholderOption) {
                        window.xDropdownSelect(root, '');
                    }
                }
            }
            openModal('assign-ticket-modal');
        }

        function submitAssignTicket() {
            var ticketId = document.getElementById('assign-ticket-id').value;
            var staffInput = document.querySelector('[name="assigned_to"]');
            var staffId = staffInput ? staffInput.value : '';
            var errorEl = document.getElementById('assign-staff-error');

            errorEl.classList.add('hidden');
            errorEl.textContent = '';

            if (!staffId) {
                errorEl.textContent = 'Please select a staff member.';
                errorEl.classList.remove('hidden');
                return;
            }

            if (window.currentAssignedStaffId && staffId === window.currentAssignedStaffId) {
                errorEl.textContent = 'This staff is already assigned to this ticket. Please select another.';
                errorEl.classList.remove('hidden');
                return;
            }

            closeModal('assign-ticket-modal');
            openModal('assign-loading');

            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/admin/tickets/assign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    assigned_to: staffId
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                closeModal('assign-loading');

                if (data.success) {
                    openModal('assign-success');

                    setTimeout(function() {
                        closeModal('assign-success');
                        closeModal('update-ticket-modal');
                        location.reload();
                    }, 1500);
                } else {
                    errorEl.textContent = data.message || 'Failed to assign ticket.';
                    errorEl.classList.remove('hidden');
                    openModal('assign-ticket-modal');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                closeModal('assign-loading');
                errorEl.textContent = 'An error occurred while assigning the ticket.';
                errorEl.classList.remove('hidden');
                openModal('assign-ticket-modal');
            });
        }
    </script>

@endsection