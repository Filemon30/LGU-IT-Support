<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createStaffUser(): User
    {
        $role = Role::factory()->staff()->create();

        $account = UserAccount::factory()->create([
            'email' => 'staff@biringancity.gov.ph',
            'password_hash' => Hash::make('password'),
        ]);

        $info = UserInformation::factory()->create();

        return User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);
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

    private function createSecondAdminUser(): User
    {
        $role = Role::factory()->admin()->create();

        $account = UserAccount::factory()->create([
            'email' => 'admin2@biringancity.gov.ph',
            'password_hash' => Hash::make('password'),
        ]);

        $info = UserInformation::factory()->create();

        return User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);
    }

    private function actingAsAdmin(): User
    {
        $user = $this->createAdminUser();

        $this->session([
            'logged_in' => true,
            'user_id' => $user->user_id,
            'role' => 'admin',
            'user_name' => $user->information->first_name . ' ' . $user->information->last_name,
            'staff_ref_num' => $user->staff_ref_num,
        ]);

        return $user;
    }

    private function actingAsStaff(): User
    {
        $user = $this->createStaffUser();

        $this->session([
            'logged_in' => true,
            'user_id' => $user->user_id,
            'role' => 'staff',
            'user_name' => $user->information->first_name . ' ' . $user->information->last_name,
            'staff_ref_num' => $user->staff_ref_num,
        ]);

        return $user;
    }

    public function test_mark_notification_as_read(): void
    {
        $user = $this->actingAsAdmin();

        $notification = Notification::factory()->unread()->create([
            'user_id' => $user->user_id,
        ]);

        $response = $this->postJson("/admin/notifications/{$notification->notification_id}/read");

        $response->assertJson([
            'success' => true,
        ]);

        $notification->refresh();
        $this->assertEquals('Read', $notification->notification_status);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $user = $this->actingAsAdmin();

        Notification::factory()->unread()->count(3)->create([
            'user_id' => $user->user_id,
        ]);

        $response = $this->postJson('/admin/notifications/read-all');

        $response->assertJson([
            'success' => true,
        ]);

        $unreadCount = Notification::where('user_id', $user->user_id)
            ->where('notification_status', 'Unread')
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    public function test_get_unread_count(): void
    {
        $user = $this->actingAsAdmin();

        Notification::factory()->unread()->count(3)->create([
            'user_id' => $user->user_id,
        ]);

        Notification::factory()->read()->count(2)->create([
            'user_id' => $user->user_id,
        ]);

        $response = $this->getJson('/admin/notifications/unread-count');

        $response->assertJson([
            'success' => true,
            'unread_count' => 3,
        ]);
    }

    public function test_mark_own_notification_only(): void
    {
        $userA = $this->actingAsAdmin();

        $userB = $this->createSecondAdminUser();

        $notification = Notification::factory()->unread()->create([
            'user_id' => $userB->user_id,
        ]);

        $response = $this->postJson("/admin/notifications/{$notification->notification_id}/read");

        $response->assertStatus(404);
    }

    public function test_notifications_index_returns_json(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/admin/notifications/unread-count');

        $response->assertJsonStructure([
            'success',
            'unread_count',
        ]);
    }

    public function test_mark_as_read_sets_read_at(): void
    {
        $user = $this->actingAsAdmin();

        $notification = Notification::factory()->unread()->create([
            'user_id' => $user->user_id,
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->postJson("/admin/notifications/{$notification->notification_id}/read");

        $response->assertJson(['success' => true]);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }
}
