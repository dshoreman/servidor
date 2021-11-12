<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $jill = [
        'password_confirmation' => 'hunter42',
    ];

    /** @test */
    public function user_can_login_with_email(): void
    {
        $user = User::factory()->create();
        \assert($user instanceof User);

        $response = $this->postJson('/api/session', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create();
        \assert($user instanceof User);

        $response = $this->postJson('/api/session', [
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $this->assertGuest();
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }
}
