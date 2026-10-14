<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(array $overrides = []): array
    {
        $role = Role::factory()->admin()->create();

        $account = UserAccount::factory()->create([
            'email' => $overrides['email'] ?? 'admin@biringancity.gov.ph',
            'password_hash' => Hash::make($overrides['password'] ?? 'password'),
        ]);

        $info = UserInformation::factory()->create();

        $user = User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        return compact('role', 'account', 'info', 'user');
    }

    private function createStaffUser(array $overrides = []): array
    {
        $role = Role::factory()->staff()->create();

        $account = UserAccount::factory()->create([
            'email' => $overrides['email'] ?? 'staff@biringancity.gov.ph',
            'password_hash' => Hash::make($overrides['password'] ?? 'password'),
        ]);

        $info = UserInformation::factory()->create();

        $user = User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        return compact('role', 'account', 'info', 'user');
    }

    private function actingAsAdmin(): void
    {
        $data = $this->createAdminUser();

        $this->session([
            'logged_in' => true,
            'user_id' => $data['user']->user_id,
            'role' => 'admin',
            'user_name' => $data['info']->first_name . ' ' . $data['info']->last_name,
            'staff_ref_num' => $data['user']->staff_ref_num,
        ]);
    }

    private function actingAsStaff(): void
    {
        $data = $this->createStaffUser();

        $this->session([
            'logged_in' => true,
            'user_id' => $data['user']->user_id,
            'role' => 'staff',
            'user_name' => $data['info']->first_name . ' ' . $data['info']->last_name,
            'staff_ref_num' => $data['user']->staff_ref_num,
        ]);
    }

    public function test_login_form_is_displayed(): void
    {
        $response = $this->get('/login', [
            'referer' => url('/'),
        ]);

        $response->assertStatus(200);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_validates_biringancity_email_domain(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'user@gmail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $this->createAdminUser(['email' => 'admin@biringancity.gov.ph', 'password' => 'correctpassword']);

        $response = $this->postJson('/login', [
            'email' => 'admin@biringancity.gov.ph',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function test_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'nonexistent@biringancity.gov.ph',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function test_login_with_inactive_account(): void
    {
        $role = Role::factory()->admin()->create();
        $account = UserAccount::factory()->create([
            'email' => 'inactive@biringancity.gov.ph',
            'password_hash' => Hash::make('password'),
        ]);
        $info = UserInformation::factory()->create();
        User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
            'status' => 'Disabled',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'inactive@biringancity.gov.ph',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function test_successful_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->createAdminUser(['email' => 'admin@biringancity.gov.ph', 'password' => 'password']);

        $response = $this->post('/login', [
            'email' => 'admin@biringancity.gov.ph',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(session('logged_in'));
        $this->assertEquals('admin', session('role'));
    }

    public function test_successful_staff_login_redirects_to_dashboard(): void
    {
        $this->createStaffUser(['email' => 'staff@biringancity.gov.ph', 'password' => 'password']);

        $response = $this->post('/login', [
            'email' => 'staff@biringancity.gov.ph',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(session('logged_in'));
        $this->assertEquals('staff', session('role'));
    }

    public function test_login_updates_last_login_at(): void
    {
        $data = $this->createAdminUser(['email' => 'admin@biringancity.gov.ph', 'password' => 'password']);

        $this->assertNull($data['account']->last_login_at);

        $this->post('/login', [
            'email' => 'admin@biringancity.gov.ph',
            'password' => 'password',
        ]);

        $data['account']->refresh();
        $this->assertNotNull($data['account']->last_login_at);
    }

    public function test_logout_invalidates_session(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertNull(session('logged_in'));
    }

    public function test_nonexistent_route_returns_404(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
    }
}
