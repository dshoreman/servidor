<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_delete_user(): array
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'deletetestuser',
            'gid' => 0,
        ])->json();

        $response = $this->authed()->deleteJson($this->endpoint($user['uid']), []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $user;
    }

    /**
     * @test
     * @depends can_delete_user
     */
    public function user_does_not_exist_after_deletion($user)
    {
        $response = $this->authed()->getJson($this->endpoint($user['uid']));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
