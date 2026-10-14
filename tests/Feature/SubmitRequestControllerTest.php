<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Category;
use App\Models\Color;
use App\Models\Division;
use App\Models\Issue;
use App\Models\Office;
use App\Models\PriorityLevel;
use App\Models\Requester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_request_form_is_displayed(): void
    {
        $response = $this->get('/submit-request');

        $response->assertStatus(200);
    }

    public function test_submit_barangay_requires_all_fields(): void
    {
        $response = $this->postJson('/submit-request/barangay', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'barangay_id',
            'category_id',
            'issue_id',
            'description',
            'secret_key',
        ]);
    }

    public function test_submit_barangay_with_invalid_secret_key(): void
    {
        $barangay = Barangay::factory()->create();
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $response = $this->postJson('/submit-request/barangay', [
            'barangay_id' => $barangay->barangay_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test issue description',
            'secret_key' => 'WRONG-SECRET-KEY',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['secret_key']);
    }

    public function test_submit_barangay_with_valid_data_creates_ticket(): void
    {
        $barangay = Barangay::factory()->create();
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $response = $this->postJson('/submit-request/barangay', [
            'barangay_id' => $barangay->barangay_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test issue description',
            'secret_key' => $barangay->secret_key_hash,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'ticket_ref_num',
        ]);
        $this->assertStringStartsWith('TKT-', $response->json('ticket_ref_num'));
    }

    public function test_submit_barangay_creates_requester_if_not_exists(): void
    {
        $barangay = Barangay::factory()->create();
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $this->assertDatabaseMissing('requesters', [
            'barangay_id' => $barangay->barangay_id,
            'requester_type' => 'Barangay',
        ]);

        $this->postJson('/submit-request/barangay', [
            'barangay_id' => $barangay->barangay_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test issue description',
            'secret_key' => $barangay->secret_key_hash,
        ]);

        $this->assertDatabaseHas('requesters', [
            'barangay_id' => $barangay->barangay_id,
            'requester_type' => 'Barangay',
            'status' => 'Active',
        ]);
    }

    public function test_submit_barangay_reuses_existing_requester(): void
    {
        $barangay = Barangay::factory()->create();
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $existingRequester = Requester::factory()->barangay()->create([
            'barangay_id' => $barangay->barangay_id,
        ]);

        $initialCount = Requester::where('barangay_id', $barangay->barangay_id)->count();

        $this->postJson('/submit-request/barangay', [
            'barangay_id' => $barangay->barangay_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test issue description',
            'secret_key' => $barangay->secret_key_hash,
        ]);

        $finalCount = Requester::where('barangay_id', $barangay->barangay_id)->count();
        $this->assertEquals($initialCount, $finalCount);
    }

    public function test_submit_office_requires_all_fields(): void
    {
        $response = $this->postJson('/submit-request/office', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'division_id',
            'category_id',
            'issue_id',
            'description',
            'secret_key',
        ]);
    }

    public function test_submit_office_with_invalid_secret_key(): void
    {
        $office = Office::factory()->create();
        $division = Division::factory()->create(['office_id' => $office->office_id]);
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $response = $this->postJson('/submit-request/office', [
            'division_id' => $division->division_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test office issue',
            'secret_key' => 'WRONG-SECRET-KEY',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['secret_key']);
    }

    public function test_submit_office_with_valid_data_creates_ticket(): void
    {
        $office = Office::factory()->create();
        $division = Division::factory()->create(['office_id' => $office->office_id]);
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
        ]);

        $response = $this->postJson('/submit-request/office', [
            'division_id' => $division->division_id,
            'category_id' => $category->category_id,
            'issue_id' => $issue->issue_id,
            'description' => 'Test office issue',
            'secret_key' => $division->secret_key_hash,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'message',
            'ticket_ref_num',
        ]);
        $this->assertStringStartsWith('TKT-', $response->json('ticket_ref_num'));
    }

    public function test_get_divisions_by_office_returns_json(): void
    {
        $office = Office::factory()->create();
        $division = Division::factory()->create([
            'office_id' => $office->office_id,
            'status' => 'Active',
        ]);

        $response = $this->getJson("/submit-request/divisions/{$office->office_id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'division_id' => $division->division_id,
            'division_name' => $division->division_name,
        ]);
    }

    public function test_get_issues_by_category_returns_json(): void
    {
        $color = Color::factory()->create();
        $category = Category::factory()->create(['color_id' => $color->color_id]);
        $priority = PriorityLevel::factory()->create();
        $issue = Issue::factory()->create([
            'category_id' => $category->category_id,
            'default_priority_level_id' => $priority->priority_level_id,
            'status' => 'Active',
        ]);

        $response = $this->getJson("/submit-request/issues/{$category->category_id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'issue_id' => $issue->issue_id,
            'description' => $issue->description,
        ]);
    }
}
