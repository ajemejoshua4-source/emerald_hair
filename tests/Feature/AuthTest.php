<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ada Test',
            'email' => 'ada@example.com',
            'phone' => '08012345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('account'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com', 'is_admin' => false]);
    }

    public function test_registration_ignores_admin_flag_injection(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true,
        ]);

        $this->assertDatabaseHas('users', ['email' => 'sneaky@example.com', 'is_admin' => false]);
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('account'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_signs_the_user_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
