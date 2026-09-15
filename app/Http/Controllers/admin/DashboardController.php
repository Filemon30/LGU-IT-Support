<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriorityLevel;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin dashboard.
     */
    public function index()
    {
        $totalTickets = Ticket::where('ticket_status', '!=', 'Soft Delete')->count();
        $pendingCount = Ticket::where('ticket_status', 'Pending')->count();
        $confirmedCount = Ticket::where('ticket_status', 'Confirmed')->count();
        $inProgressCount = Ticket::where('ticket_status', 'On Progress')->count();
        $cancelledCount = Ticket::where('ticket_status', 'Cancelled')->count();
        $unassignedCount = Ticket::whereNull('assigned_to')
            ->whereNotIn('ticket_status', ['Cancelled', 'Resolved', 'Soft Delete'])
            ->count();

        $priorityCounts = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->select('priority_level_id', DB::raw('count(*) as total'))
            ->groupBy('priority_level_id')
            ->pluck('total', 'priority_level_id');

        $priorityMap = PriorityLevel::pluck('priority_name', 'priority_level_id');
        $criticalCount = 0;
        $highCount = 0;
        $mediumCount = 0;
        $lowCount = 0;
        foreach ($priorityCounts as $pid => $count) {
            $name = $priorityMap[$pid] ?? '';
            match ($name) {
                'Critical' => $criticalCount = $count,
                'High' => $highCount = $count,
                'Medium' => $mediumCount = $count,
                'Low' => $lowCount = $count,
                default => null,
            };
        }

        $oneHourAgo = now()->subHour();

        $recentTickets = Ticket::with(['requester.barangay', 'requester.division.office', 'issue.category', 'priority'])
            ->where('ticket_status', '!=', 'Soft Delete')
            ->where('created_at', '>=', $oneHourAgo)
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($ticket) {
                $requesterType = $ticket->requester->requester_type ?? 'Unknown';
                $requesterName = 'Unknown';
                if ($requesterType === 'Barangay' && $ticket->requester->barangay) {
                    $requesterName = $ticket->requester->barangay->barangay_name;
                } elseif ($ticket->requester->division) {
                    $officeName = $ticket->requester->division->office->office_name ?? '';
                    $divName = $ticket->requester->division->division_name ?? '';
                    $requesterName = $officeName . ' - ' . $divName;
                }

                return [
                    'ticket_ref_num' => $ticket->ticket_ref_num,
                    'requester_name' => $requesterName,
                    'category_name' => $ticket->issue->category->category_name ?? '-',
                    'ticket_status' => $ticket->ticket_status,
                    'created_at' => $ticket->created_at,
                ];
            });

        $reassignmentRequests = TicketAssignment::with(['ticket.requester.barangay', 'ticket.requester.division.office', 'assignee.information'])
            ->whereHas('ticket', function ($q) {
                $q->where('ticket_status', '!=', 'Soft Delete');
            })
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($assignment) {
                $ticket = $assignment->ticket;
                $requesterType = $ticket->requester->requester_type ?? 'Unknown';
                $requesterName = 'Unknown';
                if ($requesterType === 'Barangay' && $ticket->requester->barangay) {
                    $requesterName = $ticket->requester->barangay->barangay_name;
                } elseif ($ticket->requester->division) {
                    $officeName = $ticket->requester->division->office->office_name ?? '';
                    $divName = $ticket->requester->division->division_name ?? '';
                    $requesterName = $officeName . ' - ' . $divName;
                }

                $assigneeName = 'Unassigned';
                if ($assignment->assignee && $assignment->assignee->information) {
                    $info = $assignment->assignee->information;
                    $assigneeName = $info->last_name . ', ' . $info->first_name;
                }

                return [
                    'ticket_ref_num' => $ticket->ticket_ref_num,
                    'assignee_name' => $assigneeName,
                    'ticket_status' => $ticket->ticket_status,
                    'created_at' => $assignment->created_at,
                ];
            });

        $chartData = $this->getCategoryChartData();

        return $this->ajaxView('admin.dashboard.index', compact(
            'totalTickets',
            'pendingCount',
            'confirmedCount',
            'inProgressCount',
            'cancelledCount',
            'unassignedCount',
            'criticalCount',
            'highCount',
            'mediumCount',
            'lowCount',
            'recentTickets',
            'reassignmentRequests',
            'chartData',
        ));
    }

    private function getCategoryChartData(): array
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ];

        $hardware = [];
        $software = [];
        $network = [];

        for ($i = 1; $i <= 12; $i++) {
            if ($i > $currentMonth) {
                $hardware[] = 0;
                $software[] = 0;
                $network[] = 0;
                continue;
            }

            $hw = Ticket::whereHas('issue.category', fn ($q) => $q->where('category_name', 'Hardware'))
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();

            $sw = Ticket::whereHas('issue.category', fn ($q) => $q->where('category_name', 'Software'))
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();

            $net = Ticket::whereHas('issue.category', fn ($q) => $q->where('category_name', 'Network'))
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $i)
                ->count();

            $hardware[] = $hw;
            $software[] = $sw;
            $network[] = $net;
        }

        return [
            'year' => $currentYear,
            'labels' => $months,
            'hardware' => $hardware,
            'software' => $software,
            'network' => $network,
        ];
    }

    /**
     * Admin ticket dashboard.
     */
    public function ticketDashboard()
    {
        $totalTickets = Ticket::where('ticket_status', '!=', 'Soft Delete')->count();

        $pendingCount = Ticket::where('ticket_status', 'Pending')->count();
        $confirmedCount = Ticket::where('ticket_status', 'Confirmed')->count();
        $inProgressCount = Ticket::where('ticket_status', 'On Progress')->count();
        $resolvedCount = Ticket::where('ticket_status', 'Resolved')->count();
        $unassignedCount = Ticket::whereNull('assigned_to')
            ->whereNotIn('ticket_status', ['Cancelled', 'Resolved', 'Soft Delete'])
            ->count();

        $priorityCounts = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->select('priority_level_id', DB::raw('count(*) as total'))
            ->groupBy('priority_level_id')
            ->pluck('total', 'priority_level_id');

        $priorityMap = PriorityLevel::pluck('priority_name', 'priority_level_id');
        $criticalCount = 0;
        $highCount = 0;
        $mediumCount = 0;
        $lowCount = 0;
        foreach ($priorityCounts as $pid => $count) {
            $name = $priorityMap[$pid] ?? '';
            match ($name) {
                'Critical' => $criticalCount = $count,
                'High' => $highCount = $count,
                'Medium' => $mediumCount = $count,
                'Low' => $lowCount = $count,
                default => null,
            };
        }

        $categoryCounts = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->select('categories.category_name', DB::raw('count(*) as total'))
            ->groupBy('categories.category_name')
            ->pluck('total', 'category_name');

        $hwTotal = $categoryCounts['Hardware'] ?? 0;
        $swTotal = $categoryCounts['Software'] ?? 0;
        $netTotal = $categoryCounts['Network'] ?? 0;
        $othersTotal = $categoryCounts['Others'] ?? 0;

        $hardwareStatuses = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Hardware')
            ->select('ticket_status', DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        $softwareStatuses = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Software')
            ->select('ticket_status', DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        $networkStatuses = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Network')
            ->select('ticket_status', DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        $othersStatuses = Ticket::where('ticket_status', '!=', 'Soft Delete')
            ->join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Others')
            ->select('ticket_status', DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        return $this->ajaxView('admin.tickets.ticket_dashboard', compact(
            'totalTickets',
            'pendingCount',
            'confirmedCount',
            'inProgressCount',
            'resolvedCount',
            'unassignedCount',
            'criticalCount',
            'highCount',
            'mediumCount',
            'lowCount',
            'hwTotal',
            'swTotal',
            'netTotal',
            'othersTotal',
            'hardwareStatuses',
            'softwareStatuses',
            'networkStatuses',
            'othersStatuses',
        ));
    }

    public function pollNewTickets(Request $request)
    {
        $lastCheck = $request->input('last_check');

        $tickets = Ticket::with(['requester.division.office', 'issue.category', 'priority'])
            ->where('created_at', '>', $lastCheck)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                $requesterType = $ticket->requester->requester_type ?? 'Unknown';
                $requesterName = 'Unknown';
                if ($requesterType === 'Barangay' && $ticket->requester->barangay) {
                    $requesterName = $ticket->requester->barangay->barangay_name;
                } elseif ($ticket->requester->division) {
                    $requesterName = $ticket->requester->division->office->office_name.' - '.$ticket->requester->division->division_name;
                }

                return [
                    'ticket_ref_num' => $ticket->ticket_ref_num,
                    'requester_name' => $requesterName,
                    'requester_type' => $requesterType,
                    'category_name' => $ticket->issue->category->category_name ?? '-',
                    'priority_name' => $ticket->priority->priority_name ?? '-',
                    'ticket_status' => $ticket->ticket_status,
                    'created_at' => $ticket->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'tickets' => $tickets,
            'last_check' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Render a view, returning only the content section
     * for AJAX requests.
     */
    protected function ajaxView(string $view, array $data = [])
    {
        if (
            request()->ajax() ||
            request()->header('X-Requested-With') === 'XMLHttpRequest'
        ) {

            return response(
                view($view, $data)
                    ->renderSections()['content'] ?? ''
            );
        }

        return view($view, $data);
    }
}
