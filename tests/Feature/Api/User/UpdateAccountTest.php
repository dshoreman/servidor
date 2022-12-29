<?php

namespace Tests\Feature\Api\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateAccountTest extends TestCase
{
    use RefreshDatabase;
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

    /** @test */
    public function can_update_password_without_name_or_email(): void
    {
        $request = $this->authed();
        $oldPassword = Hash::make('dogs');
        $this->user->update(['password' => $oldPassword]);

        $response = $request->putJson('/api/user', [
            'password' => 'dogs',
            'newPassword' => 'kittens!',
            'newPassword_confirmation' => 'kittens!',
        ]);

        $response->assertOk();
        $this->assertNotSame($oldPassword, $this->user->password, 'The password did not change.');
        $this->assertTrue(Hash::check('kittens!', $this->user->password), 'The password hash does not match.');
        $response->assertJsonStructure([
            'id', 'name', 'email',
            'created_at', 'updated_at',
        ]);
    }

    /** @test
     * @dataProvider passwordData
     *
     * @param array<string, mixed>                   $data
     * @param array<string, array<array-key, mixed>> $expectedErrors
     */
    public function updating_password_requires_valid_data(array $data, array $expectedErrors): void
    {
        $request = $this->authed();
        $this->user->update(['password' => Hash::make('oldpass')]);

        $response = $request->putJson('/api/user', $data);

        $response->assertStatus(422);

        foreach ($expectedErrors as $field => $errors) {
            $response->assertJsonValidationErrorFor($field);
            $response->assertJsonFragment([
                $field => array_map(static fn ($e): string => __(...$e), $errors),
            ]);
        }
    }

    /** @return array<int, mixed> */
    public function passwordData(): array
    {
        return [[
            ['password' => 'wrong'],
            ['password' => [
                ['validation.current_password'],
            ]],
        ], [
            ['password' => 'oldpass', 'newPassword' => 'a', 'newPassword_confirmation' => 'a'],
            ['newPassword' => [
                ['validation.min.string', ['attribute' => 'new password', 'min' => 8]]],
            ],
        ], [
            ['password' => 'oldpass', 'newPassword' => '12345678', 'newPassword_confirmation' => '01234567'],
            ['newPassword' => [
                ['validation.confirmed', ['attribute' => 'new password']],
            ]],
        ], [
            ['password' => 'oldpass', 'newPassword' => '12345678'],
            ['newPassword_confirmation' => [
                ['validation.required_with', ['attribute' => 'new password confirmation', 'values' => 'new password']],
            ], 'newPassword' => [
                ['validation.confirmed', ['attribute' => 'new password']],
            ]],
        ]];
    }
}
