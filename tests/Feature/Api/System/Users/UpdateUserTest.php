<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

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
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'updatetestuser',
            'gid' => 0,
        ])->json();

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => 'updatetestuser-renamed',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => 'updatetestuser-renamed']);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('user', $response);
        $this->addDeletable('group', $response);
    }

    /** @test */
    public function cannot_update_nonexistant_user()
    {
        $response = $this->authed()->putJson($this->endpoint(9032), [
            'name' => 'nousertest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
