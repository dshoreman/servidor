<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class UpdateGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown()
    {
        $this->pruneDeletable('groups');

        parent::tearDown();
    }

    /** @test */
    public function can_update_group()
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'updatetestgroup',
        ])->json();

        $response = $this->authed()->putJson($this->endpoint($group['gid']), [
            'name' => 'updatetestgroup-renamed',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => 'updatetestgroup-renamed']);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('group', $response);
    }

    /** @test */
    public function cannot_update_nonexistant_group()
    {
        $response = $this->authed()->putJson($this->endpoint(9032), [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
