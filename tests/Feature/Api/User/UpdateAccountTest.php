<?php

namespace Tests\Feature\Api\User;

use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateAccountTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function guest_attempts_respond_with_401(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }

    /** @test */
    public function can_update_name_and_email_without_current_password(): void
    {
        $request = $this->authed();
        $data = [
            'name' => $this->user->name . ' (changed)',
            'email' => 'changed-' . $this->user->email,
        ];
        $response = $request->putJson('/api/user', $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'id', 'name', 'email',
            'created_at', 'updated_at',
        ]);
        $response->assertJsonFragment($data);
    }

    /** @test */
    public function email_must_be_valid(): void
    {
        $request = $this->authed();
        $data = [
            'email' => 'notanemail',
        ];
        $response = $request->putJson('/api/user', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('email');
        $response->assertJsonFragment(['email' => [__('validation.email', ['attribute' => 'email'])]]);
    }
}
