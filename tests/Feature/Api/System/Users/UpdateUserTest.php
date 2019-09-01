<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /**
     * @var array
     */
    private $deleteUser = [];

    protected function tearDown()
    {
        $this->deleteTemporaryUsers();

        parent::tearDown();
    }

    /** @test */
    public function can_update_user()
    {
        $user = $this->authed()->postJson('/api/system/users', [
            'name' => 'updatetestuser',
            'gid' => 0,
        ])->json();

        $response = $this->authed()->putJson('/api/system/users/'.$user['uid'], [
            'name' => 'updatetestuser-renamed',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => 'updatetestuser-renamed']);

        $this->deleteUser = $response->json();

        return $response;
    }

    /**
     * @test
     * @depends can_update_user
     */
    public function update_response_contains_all_keys($response)
    {
        $response->assertJsonStructure([
            'name',
            'passwd',
            'uid',
            'gid',
            'gecos',
            'dir',
            'shell',
        ]);
    }

    /** @test */
    public function cannot_update_nonexistant_user()
    {
        $response = $this->authed()->putJson('/api/system/users/9032', [
            'name' => 'nousertest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function deleteTemporaryUsers()
    {
        if (!$user = $this->deleteUser) {
            return;
        }

        $endpoint = '/api/system/';

        $this->authed()->deleteJson($endpoint.'users/'.$user['uid']);
        $this->authed()->deleteJson($endpoint.'groups/'.$user['gid']);
    }
}
