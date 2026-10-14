<?php

namespace Tests\Unit\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureUserIsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_pass_middleware(): void
    {
        $response = $this->withSession([
            'role' => 'admin',
            'logged_in' => true,
            'user_id' => 1,
        ])->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_admin_is_redirected(): void
    {
        $response = $this->withSession([
            'role' => 'staff',
            'logged_in' => true,
            'user_id' => 1,
        ])->get(route('admin.dashboard'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(401);
    }
}
