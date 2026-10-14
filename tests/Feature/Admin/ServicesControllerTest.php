<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Color;
use App\Models\Issue;
use App\Models\PriorityLevel;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ServicesControllerTest extends TestCase
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

    public function test_admin_can_create_category(): void
    {
        $this->actingAsAdmin();

        $color = Color::factory()->create();

        $response = $this->postJson('/admin/services/store-category', [
            'category_name' => 'Test Category',
            'description' => 'A test category description',
            'icon' => 'ti ti-folder',
            'color' => $color->color_name,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('categories', [
            'category_name' => 'Test Category',
        ]);
    }

    public function test_create_category_validates_unique_name(): void
    {
        $this->actingAsAdmin();

        $color = Color::factory()->create();

        Category::factory()->create([
            'category_name' => 'Duplicate Category',
            'color_id' => $color->color_id,
        ]);

        $response = $this->postJson('/admin/services/store-category', [
            'category_name' => 'Duplicate Category',
            'description' => 'Description',
            'icon' => 'ti ti-folder',
            'color' => $color->color_name,
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_create_issue(): void
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create();
        $priority = PriorityLevel::factory()->create();

        $response = $this->postJson("/admin/services/{$category->category_id}/store-issue", [
            'description' => 'A new test issue',
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('issues', [
            'category_id' => $category->category_id,
            'description' => 'A new test issue',
        ]);
    }

    public function test_admin_can_delete_issue(): void
    {
        $this->actingAsAdmin();

        $issue = Issue::factory()->create([
            'status' => 'Active',
        ]);

        $response = $this->postJson(
            "/admin/services/{$issue->category_id}/issues/{$issue->issue_id}/delete"
        );

        $response->assertJson([
            'success' => true,
        ]);

        $issue->refresh();
        $this->assertEquals('Soft Delete', $issue->status);
    }

    public function test_admin_can_restore_issue(): void
    {
        $this->actingAsAdmin();

        $issue = Issue::factory()->create([
            'status' => 'Soft Delete',
        ]);

        $response = $this->postJson(
            "/admin/services/{$issue->category_id}/recycle-bin/{$issue->issue_id}/restore"
        );

        $response->assertJson([
            'success' => true,
        ]);

        $issue->refresh();
        $this->assertEquals('Active', $issue->status);
    }

    public function test_services_index_returns_data(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/services');

        $response->assertStatus(200);
    }
}
