<?php

namespace Tests\Feature\Admin;

use App\Models\Division;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DivisionControllerTest extends TestCase
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

    public function test_admin_can_create_divisions(): void
    {
        $this->actingAsAdmin();

        $office = Office::factory()->create();

        $response = $this->postJson("/admin/offices/{$office->office_id}/divisions", [
            'division_names' => ['Division A', 'Division B'],
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('divisions', [
            'office_id' => $office->office_id,
            'division_name' => 'Division A',
        ]);
        $this->assertDatabaseHas('divisions', [
            'office_id' => $office->office_id,
            'division_name' => 'Division B',
        ]);
    }

    public function test_create_divisions_validates_max_5(): void
    {
        $this->actingAsAdmin();

        $office = Office::factory()->create();

        $response = $this->postJson("/admin/offices/{$office->office_id}/divisions", [
            'division_names' => ['D1', 'D2', 'D3', 'D4', 'D5', 'D6'],
        ]);

        $response->assertStatus(422);
    }

    public function test_create_divisions_rejects_duplicates_within_batch(): void
    {
        $this->actingAsAdmin();

        $office = Office::factory()->create();

        $response = $this->postJson("/admin/offices/{$office->office_id}/divisions", [
            'division_names' => ['Same Name', 'Same Name'],
        ]);

        $response->assertStatus(422);
    }

    public function test_create_divisions_rejects_existing_names(): void
    {
        $this->actingAsAdmin();

        $office = Office::factory()->create();

        Division::factory()->create([
            'office_id' => $office->office_id,
            'division_name' => 'Existing Division',
        ]);

        $response = $this->postJson("/admin/offices/{$office->office_id}/divisions", [
            'division_names' => ['Existing Division'],
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_division_key_status(): void
    {
        $this->actingAsAdmin();

        $division = Division::factory()->create([
            'key_status' => 'Active',
        ]);

        $response = $this->postJson('/admin/offices/divisions/update', [
            'division_id' => $division->division_id,
            'key_status' => 'Disabled',
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $division->refresh();
        $this->assertEquals('Disabled', $division->key_status);
    }
}
