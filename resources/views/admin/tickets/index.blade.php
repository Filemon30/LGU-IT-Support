@extends('layouts.app')

@section('title', 'Tickets')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

    <script src="{{ asset('assets/js/modal.js') }}"></script>

    <div class="space-y-6">
         
        <x-card>

            <div class="flex flex-col gap-4">
                {{-- Search Row --}}
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <x-input_white
                            name="search"
                            type="text"
                            placeholder="Search by name or id"
                            icon="ti ti-search"
                            class="w-full sm:w-[20rem]"
                        />
                        <x-button
                            color="d-blue"
                            type="button"
                            class="w-full sm:w-auto h-9 text-xs"
                            onclick="filterTickets()"
                        >
                            Search
                        </x-button>
                    </div>

                    <x-button
                        color="outline-blue"
                        icon="ti ti-external-link"
                        href="{{ route('admin.tickets.dashboard') }}"
                        target="_blank"
                        class="h-8 text-xs"
                    >
                        Open Ticket Dashboard
                    </x-button>
                </div>

                {{-- Filters Row --}}
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:flex-wrap">
                    <x-dropdown
                        name="assigned_filter"
                        placeholder="All Assigned"
                        size="sm"
                        :options="['Assigned' => 'Assigned', 'Unassigned' => 'Unassigned']"
                    />
                    <x-dropdown
                        name="status_filter"
                        placeholder="All Status"
                        size="sm"
                        :options="['Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'On Progress' => 'On Progress', 'Resolved' => 'Resolved', 'Cancelled' => 'Cancelled']"
                    />
                    <x-dropdown
                        name="priority_filter"
                        placeholder="All Priorities"
                        size="sm"
                        :options="['Critical' => 'Critical', 'High' => 'High', 'Medium' => 'Medium', 'Low' => 'Low']"
                    />
                    <button
                        type="button"
                        id="resetFiltersBtn"
                        class="hidden h-8 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="resetFilters()"
                    >
                        <i class="ti ti-x text-sm"></i>
                        Reset
                    </button>
                </div>
            </div>

        </x-card>

        {{-- Staff Table --}}
        <x-card class="!p-0">
            <div class="p-4 pb-0">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-button
                        type="button"
                        color="outline-blue"
                        icon="ti ti-circle-check"
                        class="h-8 text-xs w-full sm:w-auto"
                        id="selectTicketToggle"
                        onclick="toggleSelectMode()"
                    >
                        Select Ticket
                    </x-button>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <label
                            id="selectAllLabel"
                            class="hidden items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer select-none"
                        >
                            <input
                                type="checkbox"
                                id="selectAllCheckbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                onchange="toggleSelectAll()"
                            />
                            Select All Tickets
                        </label>

                        <x-button
                            type="button"
                            color="outline-red"
                            icon="ti ti-trash"
                            class="h-8 text-xs w-full sm:w-auto"
                            id="deleteSelectedBtn"
                            style="display: none;"
                        >
                            Delete Selected Tickets
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <x-table
                    class="mt-4"
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

                            $priorityIcon = match(strtolower($ticket->priority->priority_name ?? '')) {
                                'critical' => 'ti ti-alert-circle',
                                'high' => 'ti ti-circle-arrow-up',
                                'medium' => 'ti ti-circle-half',
                                'low' => 'ti ti-circle-arrow-down',
                                default => 'ti ti-help-circle',
                            };

                            $statusColor = match($ticket->ticket_status) {
                                'Pending' => 'orange',
                                'Confirmed' => 'green',
                                'On Progress' => 'yellow',
                                'Resolved' => 'blue',
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
                            <td class="px-4 py-3 text-gray-900">
                                {{ $ticket->ticket_ref_num }}
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                @if($ticket->requester->requester_type === 'Barangay')
                                    Barangay - {{ $ticket->requester->barangay->barangay_name ?? '-' }}
                                @else
                                    Office ({{ $ticket->requester->division->office->office_name ?? '-' }}) - {{ $ticket->requester->division->division_name ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :label="$ticket->priority->priority_name ?? '-'" :color="$priorityColor" :icon="$priorityIcon" />
                            </td>
                            <td class="px-4 py-3">
                                <x-badge :label="$ticket->ticket_status" :color="$statusColor" :icon="$statusIcon" />
                            </td>
                            <td class="px-4 py-3">
                                @if($isAssigned)
                                    <x-badge label="Assigned" color="green" icon="ti ti-user" />
                                @else
                                    <x-badge label="Unassigned" color="gray" icon="ti ti-user-off" />
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ $ticket->created_at->setTimezone('Asia/Manila')->format('m/d/Y h:i A') }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <input
                                        type="checkbox"
                                        class="ticket-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                        data-ticket-id="{{ $ticket->ticket_id }}"
                                        data-ticket-status="{{ $ticket->ticket_status }}"
                                        onchange="updateSelectedCount()"
                                        style="display: none;"
                                    />
                                    <x-action_btn
                                        icon="ti ti-refresh"
                                        color="blue"
                                        title="Update Ticket"
                                        class="ticket-action-btn"
                                        data-modal-open="update-ticket-modal"
                                        data-ticket-id="{{ $ticket->ticket_id }}"
                                        data-ticket-ref="{{ $ticket->ticket_ref_num }}"
                                        data-requester="{{ $ticket->requester->requester_type === 'Barangay' ? 'Barangay - ' . ($ticket->requester->barangay->barangay_name ?? '-') : 'Office (' . ($ticket->requester->division->office->office_name ?? '-') . ') - ' . ($ticket->requester->division->division_name ?? '-') }}"
                                        data-priority="{{ $ticket->priority->priority_name ?? '-' }}"
                                        data-status="{{ $ticket->ticket_status }}"
                                        data-description="{{ $ticket->description ?? '-' }}"
                                        data-issue="{{ $ticket->issue->description ?? '-' }}"
                                        data-category="{{ $ticket->issue->category->category_name ?? '-' }}"
                                        data-assigned="{{ $isAssigned ? $ticket->assignee->information->first_name . ' ' . $ticket->assignee->information->last_name : 'Unassigned' }}"
                                        data-assigned-id="{{ $ticket->assigned_to ?? '' }}"
                                        data-date="{{ $ticket->created_at->setTimezone('Asia/Manila')->format('m/d/Y h:i A') }}"
                                    />
                                    <x-action_btn
                                        icon="ti ti-trash"
                                        color="red"
                                        title="Delete Ticket"
                                        class="ticket-action-btn"
                                        onclick="deleteSingleTicket({{ $ticket->ticket_id }}, '{{ $ticket->ticket_status }}')"
                                    />
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
            </div>

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
                <span class="text-xs text-gray-500">Date Submitted:</span>
                <span id="modal-date" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Reference:</span>
                <span id="modal-ticket-ref" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Requester:</span>
                <span id="modal-requester" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Category:</span>
                <span id="modal-category" class="text-xs font-semibold text-gray-900"></span>
            </div>

            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Issue:</span>
                <span id="modal-issue" class="text-xs font-semibold text-gray-900 text-right max-w-[70%]"></span>
            </div>
            
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-xs text-gray-500">Priority:</span>
                <span id="modal-priority"></span>
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
                <span class="text-xs text-gray-500">Description:</span>
                <span id="modal-description" class="text-xs font-semibold text-gray-900 text-right max-w-[70%]"></span>
            </div>

        </x-card>

        <div class="flex justify-end mt-4 gap-3">
            <x-button
                type="button"
                color="outline-red"
                icon="ti ti-circle-x"
                class="cancel-btn"
            >
                Cancel Ticket
            </x-button>
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

    {{-- Cancel Ticket Modal --}}
    <x-modal_form
        id="cancel-ticket-modal"
        title="Cancel Ticket"
        icon="ti ti-circle-x"
        width="max-w-sm"
    >
        <form onsubmit="return false;" id="cancel-ticket-form">
            <input type="hidden" id="cancel-ticket-id">

            <div class="mb-4">
                <x-input_white
                    name="cancellation_reason"
                    type="text"
                    placeholder="Enter Cancellation Reason"
                    icon="ti ti-message"
                    label="Enter Cancellation Reason"
                />
                <p id="cancel-reason-error" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>

            <div class="flex w-full items-center justify-end gap-3">
                <x-button
                    type="button"
                    color="outline-red"
                    icon="ti ti-circle-x"
                    onclick="submitCancelTicket()"
                >
                    Cancel Ticket
                </x-button>
            </div>
        </form>
    </x-modal_form>

    <x-loading_modal id="assign-loading" text="Assigning ticket..." />
    <x-success_modal id="assign-success" text="Ticket assigned successfully!" />
    <x-loading_modal id="cancel-loading" text="Cancelling ticket..." />
    <x-success_modal id="cancel-success" text="Ticket cancelled successfully!" />

    {{-- Delete Confirmation Modal --}}
    <x-modal_form
        id="delete-confirm-modal"
        title="Delete Ticket"
        icon="ti ti-alert-triangle"
        width="max-w-sm"
    >
        <div class="text-center">
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this ticket/s?</p>
            <div class="flex justify-center gap-3">
                <x-button
                    type="button"
                    color="outline-red"
                    data-modal-close="delete-confirm-modal"
                >
                    No
                </x-button>
                <x-button
                    type="button"
                    color="outline-green"
                    onclick="confirmDeleteSelected()"
                >
                    Yes
                </x-button>
            </div>
        </div>
    </x-modal_form>

    <x-loading_modal id="delete-loading" text="Deleting tickets..." />
    <x-success_modal id="delete-success" text="Tickets deleted successfully!" />

    {{-- Cannot Delete Modal --}}
    <x-modal_form
        id="cannot-delete-modal"
        title="Cannot Delete Ticket"
        icon="ti ti-alert-circle"
        width="max-w-sm"
    >
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-red-100">
                    <i class="ti ti-ban text-3xl text-red-500"></i>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-1 font-semibold">This ticket cannot be deleted.</p>
            <p class="text-xs text-gray-500 mb-6">Only tickets with <strong>Resolved</strong> or <strong>Cancelled</strong> status can be deleted.</p>
            
        </div>
    </x-modal_form>

    <script>
        (function () {
            function badgeInline(label, colorKey, map) {
                var c = map[colorKey] || map['gray'];
                return '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(' + c.bg + ',0.10);color:' + c.c + ';border-color:rgba(' + c.bg + ',0.30)"><i class="' + c.ic + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + label + '</span></span>';
            }

            var pColorKey = {
                critical: 'red',
                high: 'orange',
                medium: 'yellow',
                low: 'green'
            };

            var pMap = {
                red: { bg: '239,68,68', c: '#f87171', ic: 'ti ti-alert-circle' },
                orange: { bg: '249,115,22', c: '#fb923c', ic: 'ti ti-circle-arrow-up' },
                yellow: { bg: '234,179,8', c: '#facc15', ic: 'ti ti-circle-half-vertical' },
                green: { bg: '34,197,94', c: '#4ade80', ic: 'ti ti-circle-arrow-down' },
                gray: { bg: '107,114,128', c: '#9ca3af', ic: 'ti ti-help-circle' }
            };

            var sColorKey = {
                Pending: 'orange',
                Confirmed: 'green',
                'On Progress': 'yellow',
                Resolved: 'blue',
                Cancelled: 'red'
            };

            var sMap = {
                orange: { bg: '249,115,22', c: '#fb923c', ic: 'ti ti-clock' },
                green: { bg: '34,197,94', c: '#4ade80', ic: 'ti ti-circle-check' },
                yellow: { bg: '234,179,8', c: '#facc15', ic: 'ti ti-loader' },
                blue: { bg: '59,130,246', c: '#60a5fa', ic: 'ti ti-clipboard-check' },
                red: { bg: '239,68,68', c: '#f87171', ic: 'ti ti-circle-x' },
                gray: { bg: '107,114,128', c: '#9ca3af', ic: 'ti ti-help-circle' }
            };

            document.addEventListener('click', function (event) {
                var btn = event.target.closest('[data-modal-open="update-ticket-modal"]');
                if (!btn) return;

                document.getElementById('modal-date').textContent = btn.getAttribute('data-date') || '-';
                document.getElementById('modal-ticket-ref').textContent = btn.getAttribute('data-ticket-ref') || '-';
                document.getElementById('modal-requester').textContent = btn.getAttribute('data-requester') || '-';
                document.getElementById('modal-category').textContent = btn.getAttribute('data-category') || '-';
                document.getElementById('modal-issue').textContent = btn.getAttribute('data-issue') || '-';
                var priName = btn.getAttribute('data-priority') || '-';
                var priKey = pColorKey[priName.toLowerCase()] || 'gray';
                var priStyle = pMap[priKey] || pMap['gray'];
                document.getElementById('modal-priority').innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(' + priStyle.bg + ',0.10);color:' + priStyle.c + ';border-color:rgba(' + priStyle.bg + ',0.30)"><i class="' + priStyle.ic + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + priName + '</span></span>';

                var stName = btn.getAttribute('data-status') || '-';
                var stKey = sColorKey[stName] || 'gray';
                var stStyle = sMap[stKey] || sMap['gray'];
                document.getElementById('modal-status').innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(' + stStyle.bg + ',0.10);color:' + stStyle.c + ';border-color:rgba(' + stStyle.bg + ',0.30)"><i class="' + stStyle.ic + ' text-[0.7rem]"></i><span class="text-[0.7rem]">' + stName + '</span></span>';
                document.getElementById('modal-assigned').textContent = btn.getAttribute('data-assigned') || '-';
                document.getElementById('modal-description').textContent = btn.getAttribute('data-description') || '-';
                document.getElementById('assign-ticket-id').value = btn.getAttribute('data-ticket-id') || '';
                window.currentTicketAssigned = btn.getAttribute('data-assigned') !== 'Unassigned';
                window.currentAssignedStaffId = btn.getAttribute('data-assigned-id') || '';
                window.currentTicketId = btn.getAttribute('data-ticket-id') || '';
                window.currentTicketStatus = btn.getAttribute('data-status') || '';

                var buttonsRow = document.querySelector('#update-ticket-modal .flex.justify-end.mt-4');
                if (buttonsRow) {
                    buttonsRow.classList.toggle('hidden', window.currentTicketStatus === 'Cancelled');
                }
            });

            document.addEventListener('click', function (event) {
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

            document.addEventListener('click', function (event) {
                var cancelBtn = event.target.closest('.cancel-btn');
                if (cancelBtn) {
                    event.preventDefault();
                    event.stopPropagation();
                    closeModal('update-ticket-modal');
                    document.getElementById('cancel-ticket-id').value = window.currentTicketId || '';
                    document.getElementById('cancel-reason-error').classList.add('hidden');
                    var reasonInput = document.querySelector('[name="cancellation_reason"]');
                    if (reasonInput) reasonInput.value = '';
                    openModal('cancel-ticket-modal');
                }
            });

            function bindEcho() {
                if (!window.Echo) return false;

                window.Echo.channel('tickets')
                    .listen('.new-ticket', (e) => {
                        var requesterName = e.requester_type === 'Barangay'
                            ? 'Barangay - ' + (e.requester_name || '-')
                            : 'Office (' + (e.office_name || '-') + ') - ' + (e.division_name || e.requester_name || '-');

                        var date = new Date(e.created_at.replace(' ', 'T') + 'Z');
                        var opts = { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', hour12: true };
                        var timeStr = date.toLocaleTimeString('en-US', opts);
                        var dateStr = (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getDate().toString().padStart(2, '0') + '/' + date.getFullYear();
                        var formattedDate = dateStr + ' ' + timeStr;

                        var pKey = pColorKey[(e.priority_name || '').toLowerCase()] || 'gray';
                        var sKey = sColorKey[e.ticket_status] || 'gray';

                        var row = '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">' +
                            '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + e.ticket_ref_num + '</td>' +
                            '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + requesterName + '</td>' +
                            '<td class="whitespace-nowrap px-4 py-3">' + badgeInline(e.priority_name || '-', pKey, pMap) + '</td>' +
                            '<td class="whitespace-nowrap px-4 py-3">' + badgeInline(e.ticket_status, sKey, sMap) + '</td>' +
                            '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(107,114,128,0.10);color:#9ca3af;border-color:rgba(107,114,128,0.30)"><i class="ti ti-user-off text-[0.7rem]"></i><span class="text-[0.7rem]">Unassigned</span></span></td>' +
                            '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + formattedDate + '</td>' +
                            '<td class="px-4 py-3"><div class="flex items-center gap-1.5">' +
                            '<input type="checkbox" class="ticket-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" data-ticket-id="' + e.ticket_id + '" data-ticket-status="' + e.ticket_status + '" onchange="updateSelectedCount()" style="display: none;" />' +
                            '<button type="button" class="ticket-action-btn inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Ticket" data-modal-open="update-ticket-modal" data-ticket-id="' + e.ticket_id + '" data-ticket-ref="' + e.ticket_ref_num + '" data-requester="' + requesterName + '" data-priority="' + (e.priority_name || '-') + '" data-status="' + e.ticket_status + '" data-description="' + (e.description || '-') + '" data-issue="' + (e.description || '-') + '" data-category="' + (e.category_name || '-') + '" data-assigned="Unassigned" data-assigned-id="" data-date="' + formattedDate + '"><i class="ti ti-refresh text-xs text-white"></i></button>' +
                            '<button type="button" class="ticket-action-btn inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-red-500 hover:bg-red-600 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Delete Ticket" onclick="deleteSingleTicket(' + e.ticket_id + ', \'' + e.ticket_status + '\')"><i class="ti ti-trash text-xs text-white"></i></button>' +
                            '</div></td></tr>';

                        var tbody = document.querySelector('table tbody');
                        var emptyRow = tbody.querySelector('td[colspan="6"]');
                        if (emptyRow) { tbody.innerHTML = row; }
                        else { tbody.insertAdjacentHTML('afterbegin', row); }
                    })
                    .listen('.ticket-assigned', (e) => {
                        var row = document.querySelector('tr:has([data-ticket-id="' + e.ticket_id + '"])');
                        if (row) {
                            var assignCell = row.querySelectorAll('td')[4];
                            if (assignCell) {
                                assignCell.innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color:rgba(34,197,94,0.10);color:#4ade80;border-color:rgba(34,197,94,0.30)"><i class="ti ti-user text-[0.7rem]"></i><span class="text-[0.7rem]">Assigned</span></span>';
                            }
                        }
                    })
                    .listen('.ticket-soft-deleted', (e) => {
                        var row = document.querySelector('tr:has([data-ticket-id="' + e.ticket_id + '"])');
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

        function deleteSingleTicket(ticketId, status) {
            if (status === 'Pending' || status === 'Confirmed' || status === 'On Progress') {
                openModal('cannot-delete-modal');
                return;
            }
            window.pendingDeleteIds = [ticketId];
            document.getElementById('delete-confirm-modal').classList.remove('hidden');
            document.getElementById('delete-confirm-modal').classList.add('flex');
        }

        function confirmDeleteSelected() {
            var ticketIds = window.pendingDeleteIds || [];
            if (ticketIds.length === 0) {
                document.querySelectorAll('.ticket-checkbox:checked').forEach(function(cb) {
                    ticketIds.push(parseInt(cb.dataset.ticketId));
                });
            }
            window.pendingDeleteIds = null;
            if (ticketIds.length === 0) return;

            var deletableIds = [];
            var hasNonDeletable = false;
            document.querySelectorAll('.ticket-checkbox').forEach(function(cb) {
                var id = parseInt(cb.dataset.ticketId);
                if (ticketIds.indexOf(id) !== -1) {
                    var status = cb.dataset.ticketStatus;
                    if (status === 'Pending' || status === 'Confirmed' || status === 'On Progress') {
                        hasNonDeletable = true;
                    } else {
                        deletableIds.push(id);
                    }
                }
            });

            if (hasNonDeletable) {
                closeModal('delete-confirm-modal');
                openModal('cannot-delete-modal');
                return;
            }
            if (deletableIds.length === 0) return;

            closeModal('delete-confirm-modal');
            openModal('delete-loading');

            fetch('/admin/tickets/soft-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ticket_ids: deletableIds })
            })
            .then(function(r) { return r.json().then(function(body) { return { ok: r.ok, body: body }; }); })
            .then(function(result) {
                closeModal('delete-loading');
                if (result.ok && result.body.success) {
                    openModal('delete-success');
                    setTimeout(function() {
                        closeModal('delete-success');
                        location.reload();
                    }, 1500);
                } else {
                    openModal('cannot-delete-modal');
                }
            })
            .catch(function() {
                closeModal('delete-loading');
                alert('An error occurred while deleting tickets.');
            });
        }

        var selectMode = false;

        function toggleSelectMode() {
            selectMode = !selectMode;
            var toggle = document.getElementById('selectTicketToggle');
            var toggleSpan = toggle.querySelector('span');
            var deleteBtn = document.getElementById('deleteSelectedBtn');
            var deleteBtnSpan = deleteBtn.querySelector('span');
            var selectAllLabel = document.getElementById('selectAllLabel');
            var selectAllCheckbox = document.getElementById('selectAllCheckbox');
            var checkboxes = document.querySelectorAll('.ticket-checkbox');
            var actionBtns = document.querySelectorAll('.ticket-action-btn');

            if (selectMode) {
                toggleSpan.textContent = 'Cancel Selection';
                toggle.classList.remove('border-blue-200', 'text-blue-600', 'bg-blue-50');
                toggle.classList.add('border-red-200', 'text-red-600', 'bg-red-50');
                selectAllLabel.classList.remove('hidden');
                selectAllLabel.classList.add('inline-flex');
                selectAllCheckbox.checked = false;
                deleteBtn.style.display = '';
                deleteBtnSpan.textContent = 'Delete Selected Tickets';
                checkboxes.forEach(function(cb) { cb.style.display = ''; });
                actionBtns.forEach(function(btn) { btn.style.display = 'none'; });
            } else {
                toggleSpan.textContent = 'Select Ticket';
                toggle.classList.remove('border-red-200', 'text-red-600', 'bg-red-50');
                toggle.classList.add('border-blue-200', 'text-blue-600', 'bg-blue-50');
                selectAllLabel.classList.add('hidden');
                selectAllLabel.classList.remove('inline-flex');
                selectAllCheckbox.checked = false;
                deleteBtn.style.display = 'none';
                deleteBtnSpan.textContent = 'Delete Selected Tickets';
                checkboxes.forEach(function(cb) { cb.style.display = 'none'; cb.checked = false; });
                actionBtns.forEach(function(btn) { btn.style.display = ''; });
            }

            updateSelectedCount();
        }

        function toggleSelectAll() {
            var selectAllCheckbox = document.getElementById('selectAllCheckbox');
            var checkboxes = document.querySelectorAll('.ticket-checkbox');
            checkboxes.forEach(function(cb) {
                if (cb.style.display !== 'none') {
                    cb.checked = selectAllCheckbox.checked;
                }
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            var selected = document.querySelectorAll('.ticket-checkbox:checked').length;
            var totalVisible = document.querySelectorAll('.ticket-checkbox').length;
            var deleteBtn = document.getElementById('deleteSelectedBtn');
            var deleteBtnSpan = deleteBtn.querySelector('span');
            var selectAllCheckbox = document.getElementById('selectAllCheckbox');
            selectAllCheckbox.checked = selected > 0 && selected === totalVisible;
            if (selectMode && selected > 0) {
                deleteBtnSpan.textContent = 'Delete Selected Tickets (' + selected + ')';
            } else if (selectMode) {
                deleteBtnSpan.textContent = 'Delete Selected Tickets';
            }
        }

        document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
            var selected = document.querySelectorAll('.ticket-checkbox:checked');
            if (selected.length === 0) return;
            window.pendingDeleteIds = [];
            selected.forEach(function(cb) {
                window.pendingDeleteIds.push(parseInt(cb.dataset.ticketId));
            });
            document.getElementById('delete-confirm-modal').classList.remove('hidden');
            document.getElementById('delete-confirm-modal').classList.add('flex');
        });

        function filterTickets() {
            var searchInput = document.querySelector('[name="search"]');
            var assignedInput = document.querySelector('[name="assigned_filter"]');
            var statusInput = document.querySelector('[name="status_filter"]');
            var priorityInput = document.querySelector('[name="priority_filter"]');

            var params = {};
            if (searchInput && searchInput.value.trim()) params.search = searchInput.value.trim();
            if (assignedInput && assignedInput.value) params.assigned = assignedInput.value;
            if (statusInput && statusInput.value) params.status = statusInput.value;
            if (priorityInput && priorityInput.value) params.priority = priorityInput.value;

            updateResetBtn();

            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/admin/tickets/filter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(params)
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    var tbody = document.querySelector('table tbody');
                    tbody.innerHTML = data.html;
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        }

        function updateResetBtn() {
            var searchInput = document.querySelector('[name="search"]');
            var assignedInput = document.querySelector('[name="assigned_filter"]');
            var statusInput = document.querySelector('[name="status_filter"]');
            var priorityInput = document.querySelector('[name="priority_filter"]');
            var resetBtn = document.getElementById('resetFiltersBtn');

            var hasFilter = (searchInput && searchInput.value.trim()) ||
                (assignedInput && assignedInput.value) ||
                (statusInput && statusInput.value) ||
                (priorityInput && priorityInput.value);

            if (hasFilter) {
                resetBtn.classList.remove('hidden');
                resetBtn.classList.add('inline-flex');
            } else {
                resetBtn.classList.add('hidden');
                resetBtn.classList.remove('inline-flex');
            }
        }

        function resetFilters() {
            var searchInput = document.querySelector('[name="search"]');
            var assignedInput = document.querySelector('[name="assigned_filter"]');
            var statusInput = document.querySelector('[name="status_filter"]');
            var priorityInput = document.querySelector('[name="priority_filter"]');

            if (searchInput) searchInput.value = '';
            if (assignedInput) {
                assignedInput.value = '';
                var root = assignedInput.closest('details[data-dropdown]');
                if (root) window.xDropdownSelect(root, '');
            }
            if (statusInput) {
                statusInput.value = '';
                var root = statusInput.closest('details[data-dropdown]');
                if (root) window.xDropdownSelect(root, '');
            }
            if (priorityInput) {
                priorityInput.value = '';
                var root = priorityInput.closest('details[data-dropdown]');
                if (root) window.xDropdownSelect(root, '');
            }

            updateResetBtn();
            filterTickets();
        }

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
            .catch(function() {
                closeModal('assign-loading');
                errorEl.textContent = 'An error occurred while assigning the ticket.';
                errorEl.classList.remove('hidden');
                openModal('assign-ticket-modal');
            });
        }

        function submitCancelTicket() {
            var ticketId = document.getElementById('cancel-ticket-id').value;
            var reasonInput = document.querySelector('[name="cancellation_reason"]');
            var reason = reasonInput ? reasonInput.value.trim() : '';
            var errorEl = document.getElementById('cancel-reason-error');

            errorEl.classList.add('hidden');
            errorEl.textContent = '';

            if (!reason) {
                errorEl.textContent = 'Cancellation reason is required.';
                errorEl.classList.remove('hidden');
                return;
            }

            closeModal('cancel-ticket-modal');
            openModal('cancel-loading');

            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/admin/tickets/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    cancellation_reason: reason
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                closeModal('cancel-loading');
                if (data.success) {
                    openModal('cancel-success');
                    setTimeout(function() {
                        closeModal('cancel-success');
                        closeModal('update-ticket-modal');
                        location.reload();
                    }, 1500);
                } else {
                    errorEl.textContent = data.message || 'Failed to cancel ticket.';
                    errorEl.classList.remove('hidden');
                    openModal('cancel-ticket-modal');
                }
            })
            .catch(function() {
                closeModal('cancel-loading');
                errorEl.textContent = 'An error occurred while cancelling the ticket.';
                errorEl.classList.remove('hidden');
                openModal('cancel-ticket-modal');
            });
        }

        document.addEventListener('dropdown-change', function() {
            updateResetBtn();
            filterTickets();
        });

        var searchInput = document.querySelector('[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') filterTickets();
            });
            searchInput.addEventListener('input', function() {
                updateResetBtn();
                if (!searchInput.value.trim()) filterTickets();
            });
        }
    </script>

@endsection