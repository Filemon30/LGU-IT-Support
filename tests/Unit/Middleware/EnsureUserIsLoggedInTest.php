<?php

namespace Tests\Unit\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureUserIsLoggedInTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_access_protected_route(): void
    {
        $response = $this->withSession([
            'logged_in' => true,
            'role' => 'staff',
            'user_id' => 1,
        ])->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_gets_401(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(401);
    }

    public function test_guest_is_redirected_if_already_logged_in(): void
    {
        $response = $this->withSession([
            'logged_in' => true,
            'role' => 'staff',
            'user_id' => 1,
        ])->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }
}
