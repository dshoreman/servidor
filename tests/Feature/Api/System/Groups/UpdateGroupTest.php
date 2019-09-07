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
    public function guest_cannot_update_group()
    {
        exec('sudo groupadd -g 334 guestupdgrp');
        $this->addDeletable('group', 334);

        $response = $this->putJson($this->endpoint(334), [
            'name' => 'guestudpategroup',
        ]);

        $updated = $this->authed()->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonCount(1);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $updated->assertJsonFragment(['name' => 'guestupdgrp']);
    }

    /** @test */
    public function authed_user_can_update_group()
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'updatetestgroup',
        ])->json();

        $response = $this->authed()->putJson($this->endpoint($group['gid']), [
            'name' => 'updatetestgroup-renamed',
        ]);

        $response->assertOk();
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
