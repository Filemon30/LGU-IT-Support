<?php

namespace Tests\Unit\Models;

use App\Models\AdminTransaction;
use App\Models\Barangay;
use App\Models\Category;
use App\Models\Color;
use App\Models\Division;
use App\Models\Issue;
use App\Models\Notification;
use App\Models\Office;
use App\Models\PriorityLevel;
use App\Models\Requester;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_belongs_to_role(): void
    {
        $role = Role::factory()->admin()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'admin@biringancity.gov.ph']);
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $this->assertTrue($user->role->is($role));
        $this->assertEquals('Admin', $user->role->role_name);
    }

    public function test_user_has_one_information(): void
    {
        $info = UserInformation::factory()->create(['first_name' => 'Juan']);
        $account = UserAccount::factory()->create();
        $role = Role::factory()->staff()->create();
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $this->assertTrue($user->information->is($info));
        $this->assertEquals('Juan', $user->information->first_name);
    }

    public function test_user_has_one_account(): void
    {
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'staff@biringancity.gov.ph']);
        $role = Role::factory()->staff()->create();
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $this->assertTrue($user->account->is($account));
        $this->assertEquals('staff@biringancity.gov.ph', $user->account->email);
    }

    public function test_user_has_many_assigned_tickets(): void
    {
        $role = Role::factory()->admin()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'admin@biringancity.gov.ph']);
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $requester = Requester::factory()->barangay()->create();
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->create();

        Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
            'assigned_to' => $user->user_id,
        ]);

        $this->assertCount(1, $user->assignedTickets);
        $this->assertEquals($user->user_id, $user->assignedTickets->first()->assigned_to);
    }

    public function test_ticket_belongs_to_requester(): void
    {
        $barangay = Barangay::factory()->create();
        $requester = Requester::factory()->barangay()->create([
            'barangay_id' => $barangay->barangay_id,
        ]);
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertTrue($ticket->requester->is($requester));
    }

    public function test_ticket_belongs_to_issue(): void
    {
        $requester = Requester::factory()->barangay()->create();
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertTrue($ticket->issue->is($issue));
    }

    public function test_ticket_belongs_to_priority(): void
    {
        $requester = Requester::factory()->barangay()->create();
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->critical()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertTrue($ticket->priority->is($priority));
        $this->assertEquals('Critical', $ticket->priority->priority_name);
    }

    public function test_ticket_belongs_to_assignee(): void
    {
        $role = Role::factory()->staff()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'staff@biringancity.gov.ph']);
        $assignee = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $requester = Requester::factory()->barangay()->create();
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
            'assigned_to' => $assignee->user_id,
        ]);

        $this->assertTrue($ticket->assignee->is($assignee));
    }

    public function test_ticket_has_many_status_history(): void
    {
        $role = Role::factory()->admin()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'admin@biringancity.gov.ph']);
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $requester = Requester::factory()->barangay()->create();
        $issue = Issue::factory()->create();
        $priority = PriorityLevel::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $requester->requester_id,
            'issue_id' => $issue->issue_id,
            'priority_level_id' => $priority->priority_level_id,
        ]);

        TicketStatusHistory::factory()->create([
            'ticket_id' => $ticket->ticket_id,
            'changed_by' => $user->user_id,
            'old_status' => 'Pending',
            'new_status' => 'Confirmed',
        ]);

        $this->assertCount(1, $ticket->statusHistory);
        $this->assertEquals('Confirmed', $ticket->statusHistory->first()->new_status);
    }

    public function test_issue_belongs_to_category(): void
    {
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertTrue($issue->category->is($category));
    }

    public function test_issue_belongs_to_default_priority(): void
    {
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->high()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertTrue($issue->defaultPriority->is($priority));
        $this->assertEquals('High', $issue->defaultPriority->priority_name);
    }

    public function test_category_belongs_to_color(): void
    {
        $color = Color::factory()->create(['color_name' => 'Blue']);
        $category = Category::factory()->create(['color_id' => $color->color_id]);

        $this->assertTrue($category->color->is($color));
        $this->assertEquals('Blue', $category->color->color_name);
    }

    public function test_category_has_many_issues(): void
    {
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();

        Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);
        Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertCount(2, $category->issues);
    }

    public function test_barangay_has_many_requesters(): void
    {
        $barangay = Barangay::factory()->create();

        Requester::factory()->barangay()->create(['barangay_id' => $barangay->barangay_id]);
        Requester::factory()->barangay()->create(['barangay_id' => $barangay->barangay_id]);

        $this->assertCount(2, $barangay->requesters);
    }

    public function test_office_has_many_divisions(): void
    {
        $office = Office::factory()->create();

        Division::factory()->create(['office_id' => $office->office_id]);
        Division::factory()->create(['office_id' => $office->office_id]);

        $this->assertCount(2, $office->divisions);
    }

    public function test_division_belongs_to_office(): void
    {
        $office = Office::factory()->create();
        $division = Division::factory()->create(['office_id' => $office->office_id]);

        $this->assertTrue($division->office->is($office));
    }

    public function test_requester_belongs_to_barangay(): void
    {
        $barangay = Barangay::factory()->create();
        $requester = Requester::factory()->barangay()->create([
            'barangay_id' => $barangay->barangay_id,
        ]);

        $this->assertTrue($requester->barangay->is($barangay));
    }

    public function test_requester_belongs_to_division(): void
    {
        $office = Office::factory()->create();
        $division = Division::factory()->create(['office_id' => $office->office_id]);
        $requester = Requester::factory()->officeDivision()->create([
            'division_id' => $division->division_id,
        ]);

        $this->assertTrue($requester->division->is($division));
    }

    public function test_notification_belongs_to_user(): void
    {
        $role = Role::factory()->staff()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'staff@biringancity.gov.ph']);
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $notification = Notification::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $this->assertTrue($notification->user->is($user));
    }

    public function test_admin_transaction_belongs_to_handler(): void
    {
        $role = Role::factory()->admin()->create();
        $info = UserInformation::factory()->create();
        $account = UserAccount::factory()->create(['email' => 'admin@biringancity.gov.ph']);
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'user_info_id' => $info->user_info_id,
            'user_acc_id' => $account->user_acc_id,
        ]);

        $transaction = AdminTransaction::factory()->create([
            'handled_by' => $user->user_id,
        ]);

        $this->assertTrue($transaction->handler->is($user));
    }
}
