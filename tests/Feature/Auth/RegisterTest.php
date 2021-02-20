<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RequiresAuth;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    private $jill = [
        'name' => 'Jill',
        'email' => 'jill@example.com',
        'password' => 'hunter42',
        'password_confirmation' => 'hunter42',
    ];

    private $jillClean = [
        'name' => 'Jill',
        'email' => 'jill@example.com',
    ];

    /** @test */
    public function cannot_create_account_when_registration_is_disabled(): void
    {
        config(['app.registration_enabled' => false]);

        $response = $this->postJson('/api/register', $this->jill);

        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'Registration is disabled.']);
    }

    /** @test */
    public function cannot_create_account_while_logged_in(): void
    {
        $response = $this->authed()->postJson('/api/register', $this->jill);

        $response->assertRedirect('/');
    }

    /** @test */
    public function guest_can_register_an_account(): void
    {
        config(['app.registration_enabled' => true]);

        $response = $this->postJson('/api/register', $this->jill);

        $response->assertOk();
        $response->assertJson($this->jillClean);
    }
}
