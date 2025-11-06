<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnapprovedUserLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unapproved_user_cannot_login()
    {
        // Ensure exceptions are handled and converted to HTTP responses so session errors are available
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'is_approved' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

    $response->assertStatus(302);
    $this->assertGuest();

    // The framework should have flashed validation errors to the session
    $this->assertTrue(session()->has('errors'));
    $this->assertStringContainsString('not yet approved', session('errors')->first('email'));
    }

    /** @test */
    public function approved_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'is_approved' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
