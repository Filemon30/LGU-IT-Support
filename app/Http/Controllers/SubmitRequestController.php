<?php

namespace App\Http\Controllers;

use App\Events\NewTicketSubmitted;
use App\Models\Barangay;
use App\Models\Category;
use App\Models\Division;
use App\Models\Issue;
use App\Models\Office;
use App\Models\Requester;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SubmitRequestController extends Controller
{
    /**
     * Show the submit request form.
     */
    public function showSubmitRequestForm()
    {
        $barangays = Barangay::where('key_status', 'Active')
            ->orderBy('barangay_name')
            ->get();

        $offices = Office::orderBy('office_name')
            ->get();

        $divisions = Division::where('status', '!=', 'Disabled')
            ->orderBy('division_name')
            ->get();

        $categories = Category::where('status', 'Active')
            ->orderBy('category_name')
            ->get();

        $issues = Issue::where('status', 'Active')
            ->orderBy('description')
            ->get();

        return view('submit_request', compact(
            'barangays',
            'offices',
            'divisions',
            'categories',
            'issues'
        ));
    }

    /**
     * Handle barangay ticket submission.
     */
    public function submitBarangay(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'barangay_id' => 'required|exists:barangays,barangay_id',
            'category_id' => 'required|exists:categories,category_id',
            'issue_id' => 'required|exists:issues,issue_id',
            'description' => 'required|string',
            'secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $barangay = Barangay::findOrFail($request->barangay_id);

        if (strtolower($request->secret_key) !== strtolower($barangay->secret_key_hash)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'secret_key' => ['The secret key does not match this barangay.'],
                ],
            ], 422);
        }

        $requester = Requester::where('barangay_id', $barangay->barangay_id)
            ->where('status', 'Active')
            ->first();

        if (! $requester) {
            $requester = Requester::create([
                'requester_type' => 'Barangay',
                'barangay_id' => $barangay->barangay_id,
                'status' => 'Active',
            ]);
        }

        $issue = Issue::with('category')->findOrFail($request->issue_id);
        $priorityLevelId = $issue->default_priority_level_id ?? 1;

        $ticketRefNum = $this->generateTicketRefNum();

        $ticket = Ticket::create([
            'ticket_ref_num' => $ticketRefNum,
            'requester_id' => $requester->requester_id,
            'issue_id' => $request->issue_id,
            'description' => $request->description,
            'priority_level_id' => $priorityLevelId,
            'ticket_status' => 'Pending',
        ]);

        event(new NewTicketSubmitted(
            $ticket->ticket_ref_num,
            $barangay->barangay_name,
            'Barangay',
            $issue->category->category_name,
            $ticket->priority->priority_name ?? 'Medium',
            'Pending',
            $ticket->created_at->toDateTimeString()
        ));

        return response()->json([
            'success' => true,
            'message' => 'Ticket submitted successfully!',
            'ticket_ref_num' => $ticket->ticket_ref_num,
        ]);
    }

    /**
     * Handle office ticket submission.
     */
    public function submitOffice(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'division_id' => 'required|exists:divisions,division_id',
            'category_id' => 'required|exists:categories,category_id',
            'issue_id' => 'required|exists:issues,issue_id',
            'description' => 'required|string',
            'secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $division = Division::with('office')->findOrFail($request->division_id);

        if (strtolower($request->secret_key) !== strtolower($division->secret_key_hash)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'secret_key' => ['The secret key does not match this division.'],
                ],
            ], 422);
        }

        $requester = Requester::where('division_id', $division->division_id)
            ->where('status', 'Active')
            ->first();

        if (! $requester) {
            $requester = Requester::create([
                'requester_type' => 'Office Division',
                'division_id' => $division->division_id,
                'status' => 'Active',
            ]);
        }

        $issue = Issue::with('category')->findOrFail($request->issue_id);
        $priorityLevelId = $issue->default_priority_level_id ?? 1;

        $ticketRefNum = $this->generateTicketRefNum();

        $ticket = Ticket::create([
            'ticket_ref_num' => $ticketRefNum,
            'requester_id' => $requester->requester_id,
            'issue_id' => $request->issue_id,
            'description' => $request->description,
            'priority_level_id' => $priorityLevelId,
            'ticket_status' => 'Pending',
        ]);

        $requesterName = $division->office->office_name.' - '.$division->division_name;

        event(new NewTicketSubmitted(
            $ticket->ticket_ref_num,
            $requesterName,
            'Office Division',
            $issue->category->category_name,
            $ticket->priority->priority_name ?? 'Medium',
            'Pending',
            $ticket->created_at->toDateTimeString()
        ));

        return response()->json([
            'success' => true,
            'message' => 'Ticket submitted successfully!',
            'ticket_ref_num' => $ticket->ticket_ref_num,
        ]);
    }

    /**
     * Get divisions by office.
     */
    public function getDivisionsByOffice($officeId)
    {
        $divisions = Division::where('office_id', $officeId)
            ->where('status', '!=', 'Disabled')
            ->orderBy('division_name')
            ->get(['division_id', 'division_name']);

        return response()->json($divisions);
    }

    /**
     * Get issues by category.
     */
    public function getIssuesByCategory($categoryId)
    {
        $issues = Issue::where('category_id', $categoryId)
            ->where('status', 'Active')
            ->orderBy('description')
            ->get(['issue_id', 'description']);

        return response()->json($issues);
    }

    /**
     * Check if a secret key is disabled.
     */
    public function checkKeyStatus(Request $request)
    {
        $type = $request->input('type');
        $secretKey = $request->input('secret_key');

        if (!$type || !$secretKey) {
            return response()->json(['status' => 'active']);
        }

        if ($type === 'barangay') {
            $barangay = Barangay::find($request->input('barangay_id'));
            if (!$barangay) {
                return response()->json(['status' => 'active']);
            }
            if (strtolower($secretKey) !== strtolower($barangay->secret_key_hash)) {
                return response()->json(['status' => 'active']);
            }
            return response()->json(['status' => $barangay->key_status ?? 'Active']);
        } else {
            $division = Division::find($request->input('division_id'));
            if (!$division) {
                return response()->json(['status' => 'active']);
            }
            if (strtolower($secretKey) !== strtolower($division->secret_key_hash)) {
                return response()->json(['status' => 'active']);
            }
            return response()->json(['status' => $division->key_status ?? 'Active']);
        }
    }

    /**
     * Generate unique ticket reference number.
     */
    private function generateTicketRefNum(): string
    {
        do {
            $refNum = 'TKT-'.date('Y').'-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Ticket::where('ticket_ref_num', $refNum)->exists());

        return $refNum;
    }
}
