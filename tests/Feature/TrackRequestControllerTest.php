<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Category;
use App\Models\Color;
use App\Models\Issue;
use App\Models\PriorityLevel;
use App\Models\Requester;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrackRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_request_form_is_displayed(): void
    {
        $response = $this->get('/track-request');

        $response->assertStatus(200);
    }

    public function test_track_requires_ref_parameter(): void
    {
        $response = $this->postJson('/track-request', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ref']);
    }

    public function test_track_with_nonexistent_ref_returns_error(): void
    {
        $response = $this->postJson('/track-request', [
            'ref' => 'TKT-2026-99999',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'No ticket information found for this reference number.',
        ]);
    }

    public function test_track_with_valid_ref_returns_ticket_data(): void
    {
        $barangay = Barangay::factory()->create();
        $requester = Requester::factory()->barangay()->create([
            'barangay_id' => $barangay->barangay_id,
        ]);
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);
        $ticket = Ticket::factory()->pending()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        $response = $this->postJson('/track-request', [
            'ref' => $ticket->ticket_ref_num,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'ticket' => [
                'ref_num' => $ticket->ticket_ref_num,
                'status' => $ticket->ticket_status,
            ],
        ]);
    }

    public function test_track_returns_cancellation_reason_when_cancelled(): void
    {
        $barangay = Barangay::factory()->create();
        $requester = Requester::factory()->barangay()->create([
            'barangay_id' => $barangay->barangay_id,
        ]);
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);
        $ticket = Ticket::factory()->cancelled()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        $role = Role::factory()->admin()->create();
        $account = UserAccount::factory()->create([
            'email' => 'canceller@biringancity.gov.ph',
            'password_hash' => Hash::make('password'),
        ]);
        $info = UserInformation::factory()->create();
        $user = User::factory()->active()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        TicketStatusHistory::factory()->create([
            'ticket_id' => $ticket->ticket_id,
            'old_status' => 'Pending',
            'new_status' => 'cancelled',
            'changed_by' => $user->user_id,
            'remarks' => 'No longer needed by the requester.',
        ]);

        $response = $this->postJson('/track-request', [
            'ref' => $ticket->ticket_ref_num,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'ticket' => [
                'cancellation_reason' => 'No longer needed by the requester.',
            ],
        ]);
    }
}
