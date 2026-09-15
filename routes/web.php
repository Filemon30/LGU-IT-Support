<?php

use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SubmitRequestController;
use App\Http\Controllers\TrackRequestController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])
    ->name('home');

Route::get('/track-request', [TrackRequestController::class, 'trackRequest'])
    ->name('track.request');

Route::post('/track-request', [TrackRequestController::class, 'trackSubmit'])
    ->name('track.submit');

Route::post('/api/track-ticket', [TrackRequestController::class, 'trackSubmit'])
    ->name('track.api');

Route::get('/submit-request', [SubmitRequestController::class, 'showSubmitRequestForm'])
    ->name('submit.request');

Route::post('/submit-request/barangay', [SubmitRequestController::class, 'submitBarangay'])
    ->name('submit.barangay');

Route::post('/submit-request/office', [SubmitRequestController::class, 'submitOffice'])
    ->name('submit.office');

    Route::get('/submit-request/divisions/{officeId}', [SubmitRequestController::class, 'getDivisionsByOffice'])
        ->name('submit.divisions');

    Route::get('/submit-request/issues/{categoryId}', [SubmitRequestController::class, 'getIssuesByCategory'])
        ->name('submit.issues');

    Route::post('/submit-request/check-key-status', [SubmitRequestController::class, 'checkKeyStatus'])
        ->name('submit.check_key_status');

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('session.auth:guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store')
    ->middleware('session.auth:guest');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
|
| Everything inside this group requires the user to be logged in.
|
*/

Route::middleware('session.auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Client Routes (staff / barangay)
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [PageController::class, 'dashboard'])
        ->middleware('not.admin')
        ->name('dashboard');

    Route::prefix('tickets')
        ->name('tickets.')
        ->middleware('not.admin')
        ->group(function () {

            Route::get('/', [PageController::class, 'tickets'])
                ->name('index');

        });


    Route::get('/notifications', [PageController::class, 'notifications'])
        ->middleware('not.admin')
        ->name('notifications');

    Route::get('/profile', [PageController::class, 'profile'])
        ->middleware('not.admin')
        ->name('profile');

    Route::get('/settings', [PageController::class, 'settings'])
        ->middleware('not.admin')
        ->name('settings');

    Route::get('/history', [PageController::class, 'history'])
        ->middleware('not.admin')
        ->name('history');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    |
    | Everything admin-related lives under /admin.
    |
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('admin')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::prefix('tickets')
                ->name('tickets.')
                ->group(function () {

                    Route::get('/', [TicketController::class, 'index'])
                        ->name('index');

                    Route::post('/assign', [TicketController::class, 'assign'])
                        ->name('assign');

                    Route::post('/cancel', [TicketController::class, 'cancel'])
                        ->name('cancel');

                    Route::post('/soft-delete', [TicketController::class, 'softDelete'])
                        ->name('soft-delete');

                    Route::post('/filter', [TicketController::class, 'filter'])
                        ->name('filter');

                    Route::get('/dashboard', [DashboardController::class, 'ticketDashboard'])
                        ->name('dashboard');

                    Route::get('/poll', [DashboardController::class, 'pollNewTickets'])
                        ->name('poll');

                });



            Route::get('/notifications', [PageController::class, 'adminNotifications'])
                ->name('notifications');

            Route::get('/profile', [PageController::class, 'adminProfile'])
                ->name('profile');

            Route::get('/settings', [PageController::class, 'adminSettings'])
                ->name('settings');

            Route::get('/history', [PageController::class, 'adminHistory'])
                ->name('history');

            Route::get('/staff', [PageController::class, 'staff'])
                ->name('staff');

            Route::post('/staff', [StaffController::class, 'store'])
                ->name('staff.store');

            Route::post('/staff/search', [StaffController::class, 'search'])
                ->name('staff.search');

            Route::post('/staff/search-archives', [StaffController::class, 'searchArchives'])
                ->name('staff.searchArchives');

            Route::post('/staff/archive', [StaffController::class, 'archive'])
                ->name('staff.archive');

            Route::post('/staff/unarchive', [StaffController::class, 'unarchive'])
                ->name('staff.unarchive');

            Route::post('/staff/update-password', [StaffController::class, 'updatePassword'])
                ->name('staff.updatePassword');

            Route::post('/staff/update-status', [StaffController::class, 'updateStatus'])
                ->name('staff.updateStatus');

            Route::post('/staff/validate-step', [StaffController::class, 'validateStep'])
                ->name('staff.validateStep');

            Route::get('/staff/staff_information',
                [StaffController::class, 'staffInformation'])
                ->name('staff.staff_information');
            Route::get('/staff/recycle-bin',
                [StaffController::class, 'recycleBin'])
                ->name('staff.recycle_bin');

            Route::get('/barangays', [PageController::class, 'barangays'])
                ->name('barangays');

            Route::post('/barangays', [BarangayController::class, 'store'])
                ->name('barangays.store');

            Route::post('/barangays/search', [BarangayController::class, 'search'])
                ->name('barangays.search');

            Route::post('/barangays/update', [BarangayController::class, 'update'])
                ->name('barangays.update');

            Route::get('/offices', [PageController::class, 'offices'])
                ->name('offices');
            Route::post('/offices', [OfficeController::class, 'store'])
                ->name('offices.store');
            Route::post('/offices/search', [OfficeController::class, 'search'])
                ->name('offices.search');
            Route::post('/offices/load', [OfficeController::class, 'loadOffices'])
                ->name('offices.load');
            Route::get('/offices/{office}/divisions',
                [OfficeController::class, 'officeDivisions'])
                ->name('offices.office_divisions');
            Route::post('/offices/{office}/divisions',
                [DivisionController::class, 'store'])
                ->name('offices.divisions.store');
            Route::post('/offices/divisions/update',
                [DivisionController::class, 'update'])
                ->name('offices.divisions.update');

            Route::get('/services', [ServicesController::class, 'index'])
                ->name('services');

            Route::get('/services/{categoryId}/issues', [ServicesController::class, 'issues'])
                ->name('services.issues');

            Route::get('/services/{categoryId}/recycle-bin', [ServicesController::class, 'recycleBin'])
                ->name('services.recycle_bin');

            Route::post('/services/{categoryId}/recycle-bin/{issueId}/restore', [ServicesController::class, 'restoreIssue'])
                ->name('services.restore_issue');

            Route::post('/services/store-category', [ServicesController::class, 'storeCategory'])
                ->name('services.store-category');

            Route::post('/services/{categoryId}/store-issue', [ServicesController::class, 'storeIssue'])
                ->name('services.store-issue');

            Route::post('/services/{categoryId}/issues/{issueId}/delete', [ServicesController::class, 'deleteIssue'])
                ->name('services.delete-issue');

            Route::get('/reports', [PageController::class, 'reports'])
                ->name('reports');

        });

});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| 404 Fallback
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This is OUTSIDE the authentication middleware.
|
| Therefore:
|
| Existing protected URL + not logged in = 401
| Non-existing URL = 404
|
*/

Route::fallback([PageController::class, 'notFound']);
