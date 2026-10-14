<?php

namespace Tests\Feature\Admin;

use App\Models\Barangay;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BarangayControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $pdo = DB::connection()->getPdo();
        $pdo->sqliteCreateFunction('SUBSTRING_INDEX', function (string $string, string $delimiter, int $number): string {
            $parts = explode($delimiter, $string);
            if ($number > 0) {
                return implode($delimiter, array_slice($parts, 0, $number));
            }
            return implode($delimiter, array_slice($parts, $number));
        }, 3);
    }

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

    public function test_admin_can_create_barangay(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/admin/barangays', [
            'barangay_name' => 'Test Barangay',
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('barangays', [
            'barangay_name' => 'Test Barangay',
        ]);

        $barangay = Barangay::where('barangay_name', 'Test Barangay')->first();
        $this->assertStringStartsWith('BRGY-', $barangay->barangay_ref_num);
    }

    public function test_create_barangay_validates_unique_name(): void
    {
        $this->actingAsAdmin();

        Barangay::factory()->create([
            'barangay_name' => 'Duplicate Barangay',
        ]);

        $response = $this->postJson('/admin/barangays', [
            'barangay_name' => 'Duplicate Barangay',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_barangay(): void
    {
        $this->actingAsAdmin();

        $barangay = Barangay::factory()->create([
            'barangay_name' => 'Original Name',
            'key_status' => 'Active',
        ]);

        $response = $this->postJson('/admin/barangays/update', [
            'barangay_id' => $barangay->barangay_id,
            'barangay_name' => 'Updated Name',
            'key_status' => 'Disabled',
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $barangay->refresh();
        $this->assertEquals('Updated Name', $barangay->barangay_name);
        $this->assertEquals('Disabled', $barangay->key_status);
    }

    public function test_search_returns_html(): void
    {
        $this->actingAsAdmin();

        Barangay::factory()->create(['barangay_name' => 'Searchable Barangay']);

        $response = $this->postJson('/admin/barangays/search', [
            'search' => 'Searchable',
        ]);

        $response->assertJsonStructure([
            'success',
            'html',
        ]);
    }

    public function test_create_barangay_generates_secret_key(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/admin/barangays', [
            'barangay_name' => 'Key Test Barangay',
        ]);

        $response->assertJson(['success' => true]);

        $barangay = Barangay::where('barangay_name', 'Key Test Barangay')->first();
        $this->assertNotEmpty($barangay->secret_key_hash);
        $this->assertNotNull($barangay->secret_key_hash);
    }
}
