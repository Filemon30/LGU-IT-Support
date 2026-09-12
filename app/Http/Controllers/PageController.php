<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Office;
use App\Models\PriorityLevel;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    /**
     * Landing page.
     */
    public function home()
    {
        return view('landing_page');
    }

    /**
     * Client dashboard (staff / barangay).
     */
    public function dashboard()
    {
        $role = session('role', 'staff');

        $view = $role === 'barangay'
            ? 'clients.dashboards.barangay'
            : 'clients.dashboards.staff';

        return $this->ajaxView($view);
    }

    /**
     * Client tickets.
     */
    public function tickets()
    {
        return $this->ajaxView('clients.tickets.index');
    }

    /**
     * Client knowledge base.
     */
    public function knowledge()
    {
        return $this->ajaxView('clients.knowledge.index');
    }

    /**
     * Client notifications.
     */
    public function notifications()
    {
        return $this->ajaxView('clients.notifications.index');
    }

    /**
     * Client profile.
     */
    public function profile()
    {
        return $this->ajaxView('clients.profile.index');
    }

    /**
     * Client settings.
     */
    public function settings()
    {
        return $this->ajaxView('clients.settings.index');
    }

    /**
     * Client history.
     */
    public function history()
    {
        return $this->ajaxView('clients.history.index');
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard()
    {
        $totalTickets = Ticket::count();
        $pendingCount = Ticket::where('ticket_status', 'Pending')->count();
        $confirmedCount = Ticket::where('ticket_status', 'Confirmed')->count();
        $inProgressCount = Ticket::where('ticket_status', 'On Progress')->count();
        $cancelledCount = Ticket::where('ticket_status', 'Cancelled')->count();
        $unassignedCount = Ticket::whereNull('assigned_to')
            ->whereNotIn('ticket_status', ['Cancelled', 'Resolved'])
            ->count();

        $priorityCounts = Ticket::select('priority_level_id', DB::raw('count(*) as total'))
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

        $recentTickets = Ticket::with(['requester.barangay', 'requester.division.office', 'issue.category', 'priority'])
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
     * Admin tickets.
     */
    public function adminTickets()
    {
        return $this->ajaxView('admin.tickets.index');
    }

    public function adminTicketDashboard()
    {
        $totalTickets = Ticket::count();

        $pendingCount = Ticket::where('ticket_status', 'Pending')->count();
        $confirmedCount = Ticket::where('ticket_status', 'Confirmed')->count();
        $inProgressCount = Ticket::where('ticket_status', 'On Progress')->count();
        $resolvedCount = Ticket::where('ticket_status', 'Resolved')->count();
        $unassignedCount = Ticket::whereNull('assigned_to')
            ->whereNotIn('ticket_status', ['Cancelled', 'Resolved'])
            ->count();

        $priorityCounts = Ticket::select('priority_level_id', \DB::raw('count(*) as total'))
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

        $categoryCounts = Ticket::join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->select('categories.category_name', \DB::raw('count(*) as total'))
            ->groupBy('categories.category_name')
            ->pluck('total', 'category_name');

        $hwTotal = $categoryCounts['Hardware'] ?? 0;
        $swTotal = $categoryCounts['Software'] ?? 0;
        $netTotal = $categoryCounts['Network'] ?? 0;

        $hardwareStatuses = Ticket::join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Hardware')
            ->select('ticket_status', \DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        $softwareStatuses = Ticket::join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Software')
            ->select('ticket_status', \DB::raw('count(*) as total'))
            ->groupBy('ticket_status')
            ->pluck('total', 'ticket_status');

        $networkStatuses = Ticket::join('issues', 'tickets.issue_id', '=', 'issues.issue_id')
            ->join('categories', 'issues.category_id', '=', 'categories.category_id')
            ->where('categories.category_name', 'Network')
            ->select('ticket_status', \DB::raw('count(*) as total'))
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
            'hardwareStatuses',
            'softwareStatuses',
            'networkStatuses',
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
                    'issue_name' => $ticket->issue->issue_name ?? '-',
                    'priority_name' => $ticket->priority->priority_level_name ?? '-',
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
     * Admin knowledge base.
     */
    public function adminKnowledge()
    {
        return $this->ajaxView('admin.knowledge.index');
    }

    /**
     * Admin notifications.
     */
    public function adminNotifications()
    {
        return $this->ajaxView('admin.notifications.index');
    }

    /**
     * Admin profile.
     */
    public function adminProfile()
    {
        return $this->ajaxView('admin.profile.index');
    }

    /**
     * Admin settings.
     */
    public function adminSettings()
    {
        return $this->ajaxView('admin.settings.index');
    }

    /**
     * Admin history.
     */
    public function adminHistory()
    {
        return $this->ajaxView('admin.history.index');
    }

    /**
     * Admin staff.
     */
    public function staff()
    {
        $staff = User::with(['role', 'information'])
            ->whereIn('status', ['Active', 'Disabled'])
            ->orderBy('user_id')
            ->get();

        $totalStaff = $staff->count();
        $activeStaff = $staff->where('status', 'Active')->count();
        $deactivatedStaff = $staff->where('status', 'Disabled')->count();

        $roles = Role::all();

        $barangays = Barangay::where('key_status', 'Active')->get();

        $genderColumn = DB::select("SHOW COLUMNS FROM user_informations LIKE 'gender'")[0] ?? null;
        $genderOptions = [];
        if ($genderColumn && preg_match("/enum\((.+)\)/i", $genderColumn->Type, $matches)) {
            $genderOptions = array_map(function ($val) {
                return trim($val, "'");
            }, explode(',', $matches[1]));
        }

        return $this->ajaxView('admin.staff.index', compact('staff', 'totalStaff', 'activeStaff', 'deactivatedStaff', 'roles', 'genderOptions', 'barangays'));
    }

    /**
     * Admin barangays.
     */
    public function barangays()
    {
        $barangays = Barangay::orderBy('barangay_id')->get();

        return $this->ajaxView('admin.barangays.index', compact('barangays'));
    }

    /**
     * Admin Offices.
     */
    public function offices()
    {
        $offices = Office::withCount('divisions')->orderBy('office_id')->get();

        return $this->ajaxView('admin.offices.index', compact('offices'));
    }

    /**
     * Admin services.
     */
    public function services()
    {
        return $this->ajaxView('admin.services.index');
    }

    /**
     * Admin reports.
     */
    public function reports()
    {
        return $this->ajaxView('admin.reports.index');
    }

    /**
     * 404 fallback.
     */
    public function notFound()
    {
        return response()->view(
            'error.404',
            [],
            404
        );
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
