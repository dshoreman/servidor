<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->postJson('/api/session', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }
}
