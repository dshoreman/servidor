<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\User;
use Tests\RequiresAuth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    private $jill = [
        'password_confirmation' => 'hunter42',
    ];

    /** @test */
    public function user_can_login_with_email(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_can_logout(): void
    {
        $response = $this->authed()->postJson('/api/logout');

        $response->assertNoContent();
    }
}
