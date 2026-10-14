<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        $role = Role::factory()->admin()->create();

        $account = UserAccount::factory()->create([
            'email' => 'admin@biringancity.gov.ph',
            'password_hash' => Hash::make('password'),
        ]);

        $info = UserInformation::factory()->create();

        return User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);
    }

    private function actingAsAdmin(): void
    {
        $user = $this->createAdminUser();

        $this->session([
            'logged_in' => true,
            'user_id' => $user->user_id,
            'role' => 'admin',
            'user_name' => $user->information->first_name . ' ' . $user->information->last_name,
            'staff_ref_num' => $user->staff_ref_num,
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_ticket_dashboard_returns_data(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/tickets/dashboard');

        $response->assertStatus(200);
    }

    public function test_poll_returns_new_tickets(): void
    {
        $this->actingAsAdmin();

        $lastCheck = now()->subMinutes(5)->toDateTimeString();
        $response = $this->getJson('/admin/tickets/poll?last_check=' . urlencode($lastCheck));

        $response->assertJsonStructure([
            'tickets',
            'last_check',
        ]);
    }
}
