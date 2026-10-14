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

class OfficeControllerTest extends TestCase
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

    public function test_admin_can_create_office(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/admin/offices', [
            'office_name' => 'Test Office',
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('offices', [
            'office_name' => 'Test Office',
        ]);

        $office = Office::where('office_name', 'Test Office')->first();
        $this->assertStringStartsWith('OFF-', $office->office_ref_num);
    }

    public function test_create_office_validates_unique_name(): void
    {
        $this->actingAsAdmin();

        Office::factory()->create([
            'office_name' => 'Duplicate Office',
        ]);

        $response = $this->postJson('/admin/offices', [
            'office_name' => 'Duplicate Office',
        ]);

        $response->assertStatus(422);
    }

    public function test_search_returns_html(): void
    {
        $this->actingAsAdmin();

        Office::factory()->create(['office_name' => 'Searchable Office']);

        $response = $this->postJson('/admin/offices/search', [
            'search' => 'Searchable',
        ]);

        $response->assertJsonStructure([
            'success',
            'html',
        ]);
    }

    public function test_load_offices_returns_data(): void
    {
        $this->actingAsAdmin();

        Office::factory()->create();

        $response = $this->postJson('/admin/offices/load');

        $response->assertJsonStructure([
            'success',
            'html',
            'office_count',
        ]);
    }

    public function test_office_has_divisions_count(): void
    {
        $this->actingAsAdmin();

        $office = Office::factory()->create();

        Division::factory()->count(3)->create([
            'office_id' => $office->office_id,
        ]);

        $response = $this->postJson('/admin/offices/load');

        $response->assertJson([
            'success' => true,
            'office_count' => 1,
            'division_count' => 3,
        ]);
    }
}
