<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
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
        return $this->ajaxView('admin.dashboard.index');
    }

    /**
     * Admin tickets.
     */
    public function adminTickets()
    {
        return $this->ajaxView('admin.tickets.index');
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

        $barangays = \App\Models\Barangay::where('key_status', 'Active')->get();

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
        $barangays = \App\Models\Barangay::orderBy('barangay_id')->get();

        return $this->ajaxView('admin.barangays.index', compact('barangays'));
    }

    /**
     * Admin Offices.
     */
    public function offices()
    {
        return $this->ajaxView('admin.offices.index');
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
