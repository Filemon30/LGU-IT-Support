<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    /**
     * Show the track request form.
     */
    public function trackRequest()
    {
        return view('track_request');
    }

    /**
     * Handle track request - look up ticket by reference number via AJAX.
     */
    public function trackSubmit(Request $request)
    {
        $request->validate([
            'ref' => 'required|string',
        ]);

        $refNum = $request->input('ref');

        $ticket = Ticket::with([
            'requester.barangay',
            'requester.division.office',
            'issue.category.service',
            'priority',
            'assignee',
        ])
        ->where('ticket_ref_num', $refNum)
        ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'No ticket information found for this reference number.',
            ]);
        }

        $cancellationReason = null;
        if (strtolower($ticket->ticket_status) === 'cancelled') {
            $cancelledHistory = TicketStatusHistory::where('ticket_id', $ticket->ticket_id)
                ->where('new_status', 'cancelled')
                ->latest()
                ->first();
            $cancellationReason = $cancelledHistory ? $cancelledHistory->remarks : null;
        }

        $requesterType = $ticket->requester->requester_type ?? '';
        $requesterName = '';
        if ($requesterType === 'Barangay' && $ticket->requester->barangay) {
            $requesterName = $ticket->requester->barangay->barangay_name;
        } elseif ($requesterType === 'Office Division' && $ticket->requester->division) {
            $requesterName = $ticket->requester->division->division_name;
            if ($ticket->requester->division->office) {
                $requesterName = $ticket->requester->division->office->office_name . ' - ' . $requesterName;
            }
        }

        return response()->json([
            'success' => true,
            'ticket' => [
                'ref_num' => $ticket->ticket_ref_num,
                'date_requested' => $ticket->created_at ? $ticket->created_at->format('M d, Y') : '-',
                'requester_type' => ucfirst(str_replace('_', ' ', $requesterType)),
                'requester_name' => $requesterName,
                'category' => $ticket->issue->category->category_name ?? '-',
                'priority' => $ticket->priority->priority_name ?? '-',
                'status' => $ticket->ticket_status,
                'description' => $ticket->description ?? '-',
                'cancellation_reason' => $cancellationReason,
            ],
        ]);
    }
}
