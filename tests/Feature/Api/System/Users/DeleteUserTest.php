<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_delete_user(): array
    {
        $user = $this->authed()->postJson('/api/system/users', [
            'name' => 'deletetestuser',
            'gid' => 0,
        ])->json();

        $response = $this->authed()->deleteJson('/api/system/users/'.$user['uid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $user;
    }

    /**
     * @test
     * @depends can_delete_user
     */
    public function user_does_not_exist_after_deletion($user)
    {
        $response = $this->authed()->getJson('/api/system/users/'.$user['uid']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
