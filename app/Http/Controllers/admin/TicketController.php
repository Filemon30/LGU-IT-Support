<?php

namespace App\Http\Controllers\Admin;

use App\Events\TicketAssigned;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['requester.barangay', 'requester.division', 'issue.category', 'priority', 'assignee.information'])
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

    public function filter(Request $request)
    {
        $query = Ticket::with(['requester.barangay', 'requester.division', 'issue.category', 'priority', 'assignee.information'])
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
            $requesterName = $ticket->requester->requester_type === 'Barangay'
                ? ($ticket->requester->barangay->barangay_name ?? '-')
                : ($ticket->requester->division->division_name ?? '-');
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
                'yellow' => '234, 179, 8',
                'blue' => '59, 130, 246',
                'cyan' => '6, 182, 212',
                'green' => '34, 197, 94',
                'red' => '239, 68, 68',
                default => '107, 114, 128',
            };
            $statusHex = match($statusColor) {
                'yellow' => '#facc15',
                'blue' => '#60a5fa',
                'cyan' => '#22d3ee',
                'green' => '#4ade80',
                'red' => '#f87171',
                default => '#9ca3af',
            };

            $html .= '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($ticket->ticket_ref_num) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($requesterName) . '</td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' . $priorityRgb . ', 0.10); color: ' . $priorityHex . '; border-color: rgba(' . $priorityRgb . ', 0.30);"><span class="text-[0.7rem]">' . e($ticket->priority->priority_name ?? '-') . '</span></span></td>';
            $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(' . $statusRgb . ', 0.10); color: ' . $statusHex . '; border-color: rgba(' . $statusRgb . ', 0.30);"><i class="' . $statusIcon . ' text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($ticket->ticket_status) . '</span></span></td>';

            if ($isAssigned) {
                $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);"><i class="ti ti-user text-[0.7rem]"></i><span class="text-[0.7rem]">' . e($assignedName) . '</span></span></td>';
            } else {
                $html .= '<td class="whitespace-nowrap px-4 py-3"><span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(107, 114, 128, 0.10); color: #9ca3af; border-color: rgba(107, 114, 128, 0.30);"><i class="ti ti-user-off text-[0.7rem]"></i><span class="text-[0.7rem]">Unassigned</span></span></td>';
            }

            $html .= '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' . e($date) . '</td>';
            $html .= '<td class="px-4 py-3"><div class="flex items-center gap-1.5"><button type="button" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Ticket"><i class="ti ti-refresh text-xs text-white"></i></button></div></td>';
            $html .= '</tr>';
        }

        if ($tickets->isEmpty()) {
            $html = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No tickets found.</td></tr>';
        }

        return response()->json(['success' => true, 'html' => $html]);
    }
}
