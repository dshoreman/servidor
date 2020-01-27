<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class UpdateGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown(): void
    {
        $this->pruneDeletable('groups');

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_update_group(): void
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
    public function authed_user_can_update_group(): void
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
    public function authed_user_can_remove_all_users_from_group(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'groupwithmembers',
        ]);

        $this->addDeletable('group', $group);
        $users = ['bin', 'daemon', 'games'];
        $gid = $group->json()['gid'];

        $group = $this->authed()->putJson($this->endpoint($gid), [
            'name' => 'groupwithmembers',
            'users' => $users,
        ]);

        $group->assertOk();
        $group->assertJsonFragment(['users' => $users]);
        $group->assertJsonStructure($this->expectedKeys);

        $response = $this->authed()->putJson($this->endpoint($gid), [
            'name' => 'groupwithmembers',
            'users' => [],
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['users' => []]);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function cannot_update_nonexistant_group(): void
    {
        $response = $this->authed()->putJson($this->endpoint(9032), [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
