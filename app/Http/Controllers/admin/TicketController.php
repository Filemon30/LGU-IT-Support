<?php

namespace App\Http\Controllers\Admin;

use App\Events\TicketAssigned;
use App\Events\TicketSoftDeleted;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['requester.barangay', 'requester.division', 'issue.category', 'priority', 'assignee.information'])
            ->where('ticket_status', '!=', 'Soft Delete')
            ->orderBy('created_at', 'asc')
            ->get();

        $staff = User::with(['information'])
            ->where('status', 'Active')
            ->whereHas('role', function ($q) {
                $q->where('role_name', 'Staff');
            })
            ->get();

        return view('admin.tickets.index', compact('tickets', 'staff'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,ticket_id',
            'assigned_to' => 'required|integer|exists:users,user_id',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'ticket_status' => 'Confirmed',
        ]);

        $assignee = User::with('information')->find($request->assigned_to);
        $assignedName = $assignee->information->first_name . ' ' . $assignee->information->last_name;

        event(new TicketAssigned(
            $ticket->ticket_id,
            $ticket->ticket_ref_num,
            $assignedName,
            'Confirmed'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Ticket assigned successfully.',
        ]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,ticket_id',
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $oldStatus = $ticket->ticket_status;

        $ticket->update([
            'ticket_status' => 'Cancelled',
            'cancelled_at' => now(),
        ]);

        TicketStatusHistory::create([
            'ticket_id' => $ticket->ticket_id,
            'old_status' => $oldStatus,
            'new_status' => 'Cancelled',
            'changed_by' => session('user_id'),
            'remarks' => $request->cancellation_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket cancelled successfully.',
        ]);
    }

    public function softDelete(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'integer|exists:tickets,ticket_id',
        ]);

        $ticketIds = $request->ticket_ids;

        $tickets = Ticket::with('issue.category')
            ->whereIn('ticket_id', $ticketIds)
            ->get();

        $nonDeletable = $tickets->filter(fn($t) => !in_array($t->ticket_status, ['Resolved', 'Cancelled']));
        if ($nonDeletable->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete tickets with Pending, Confirmed, or On Progress status.',
            ], 422);
        }

        $oldStatuses = $tickets->pluck('ticket_status', 'ticket_id');

        Ticket::whereIn('ticket_id', $ticketIds)->update([
            'ticket_status' => 'Soft Delete',
        ]);

        foreach ($tickets as $ticket) {
            TicketStatusHistory::create([
                'ticket_id' => $ticket->ticket_id,
                'old_status' => $oldStatuses[$ticket->ticket_id] ?? 'Pending',
                'new_status' => 'Soft Delete',
                'changed_by' => session('user_id'),
                'remarks' => 'Ticket soft deleted by admin.',
            ]);

            event(new TicketSoftDeleted(
                $ticket->ticket_id,
                $ticket->ticket_ref_num,
                $oldStatuses[$ticket->ticket_id] ?? 'Pending',
                $ticket->issue->category->category_name ?? ''
            ));
        }

        return response()->json([
            'success' => true,
            'message' => count($ticketIds) . ' ticket(s) deleted successfully.',
        ]);
    }

    public function filter(Request $request)
    {
        $query = Ticket::with(['requester.barangay', 'requester.division', 'issue.category', 'priority', 'assignee.information'])
            ->where('ticket_status', '!=', 'Soft Delete')
            ->orderBy('created_at', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_ref_num', 'like', "%{$search}%")
                    ->orWhereHas('requester', function ($q2) use ($search) {
                        $q2->whereHas('barangay', function ($q3) use ($search) {
                            $q3->where('barangay_name', 'like', "%{$search}%");
                        })->orWhereHas('division', function ($q3) use ($search) {
                            $q3->where('division_name', 'like', "%{$search}%");
                        });
                    });
            });
        }

        if ($request->filled('priority')) {
            $query->whereHas('priority', function ($q) use ($request) {
                $q->where('priority_name', $request->priority);
            });
        }

        if ($request->filled('status')) {
            $query->where('ticket_status', $request->status);
        }

        if ($request->filled('assigned')) {
            if ($request->assigned === 'Assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($request->assigned === 'Unassigned') {
                $query->whereNull('assigned_to');
            }
        }

        $tickets = $query->get();

        $html = '';

        foreach ($tickets as $ticket) {
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
            $requesterName = $ticket->requester->requester_type === 'Barangay'
                ? 'Barangay - ' . ($ticket->requester->barangay->barangay_name ?? '-')
                : 'Office (' . ($ticket->requester->division->office->office_name ?? '-') . ') - ' . ($ticket->requester->division->division_name ?? '-');
            $assignedName = $isAssigned
                ? $ticket->assignee->information->first_name . ' ' . $ticket->assignee->information->last_name
                : 'Unassigned';
            $date = $ticket->created_at->setTimezone('Asia/Manila')->format('m/d/Y h:i A');

            $priorityRgb = match($priorityColor) {
                'red' => '239, 68, 68',
                'orange' => '249, 115, 22',
                'yellow' => '234, 179, 8',
                'green' => '34, 197, 94',
                default => '107, 114, 128',
            };
            $priorityHex = match($priorityColor) {
                'red' => '#f87171',
                'orange' => '#fb923c',
                'yellow' => '#facc15',
                'green' => '#4ade80',
                default => '#9ca3af',
            };

            $statusRgb = match($statusColor) {
                'orange' => '249, 115, 22',
                'green' => '34, 197, 94',
                'yellow' => '234, 179, 8',
                'blue' => '59, 130, 246',
                'red' => '239, 68, 68',
                default => '107, 114, 128',
            };
            $statusHex = match($statusColor) {
                'orange' => '#fb923c',
                'green' => '#4ade80',
                'yellow' => '#facc15',
                'blue' => '#60a5fa',
                'red' => '#f87171',
                default => '#9ca3af',
            };

            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($ticket->ticket_ref_num) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($requesterName) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' . $priorityRgb . ', 0.10); color: ' . $priorityHex . '; border-color: rgba(' . $priorityRgb . ', 0.30);"><i class="' . $priorityIcon . ' text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($ticket->priority->priority_name ?? '-') . '</span></span></td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' . $statusRgb . ', 0.10); color: ' . $statusHex . '; border-color: rgba(' . $statusRgb . ', 0.30);"><i class="' . $statusIcon . ' text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($ticket->ticket_status) . '</span></span></td>';

            if ($isAssigned) {
                $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);"><i class="ti ti-user text-[0.7rem]"></i><span class="text-[0.7rem]">Assigned</span></span></td>';
            } else {
                $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(107, 114, 128, 0.10); color: #9ca3af; border-color: rgba(107, 114, 128, 0.30);"><i class="ti ti-user-off text-[0.7rem]"></i><span class="text-[0.7rem]">Unassigned</span></span></td>';
            }

            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($date) . '</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5">';
            $html .= '<input type="checkbox" class="ticket-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" data-ticket-id="' . $ticket->ticket_id . '" onchange="updateSelectedCount()" style="display: none;" />';
            $html .= '<button type="button" class="ticket-action-btn inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Ticket" data-modal-open="update-ticket-modal" data-ticket-id="' . $ticket->ticket_id . '" data-ticket-ref="' . e($ticket->ticket_ref_num) . '" data-requester="' . e($requesterName) . '" data-priority="' . e($ticket->priority->priority_name ?? '-') . '" data-status="' . e($ticket->ticket_status) . '" data-description="' . e($ticket->description ?? '-') . '" data-assigned="' . e($assignedName) . '" data-assigned-id="' . ($ticket->assigned_to ?? '') . '" data-date="' . e($date) . '"><i class="ti ti-refresh text-xs text-white"></i></button>';
            $html .= '<button type="button" class="ticket-action-btn inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-red-500 hover:bg-red-600 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Delete Ticket" onclick="deleteSingleTicket(' . $ticket->ticket_id . ')"><i class="ti ti-trash text-xs text-white"></i></button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if ($tickets->isEmpty()) {
            $html = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No tickets found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html]);
    }
}
