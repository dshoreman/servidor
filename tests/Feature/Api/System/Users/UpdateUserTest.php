<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown()
    {
        $this->pruneDeletable(['users', 'groups']);

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

        $this->addDeletable('user', $response);
        $this->addDeletable('group', $response);

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
}
