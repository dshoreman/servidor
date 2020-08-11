<?php

namespace Tests\Feature\Api\User;

use Tests\RequiresAuth;
use Tests\TestCase;

class ShowProfileTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function guest_attempts_respond_with_401(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }

    /** @test */
    public function responds_with_user_data_when_authed(): void
    {
        $response = $this->authed()->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonStructure([
            'id', 'name', 'email',
            'created_at', 'updated_at',
        ]);
        $response->assertJsonFragment([
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
    }
}
