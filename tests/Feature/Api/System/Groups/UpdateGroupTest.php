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
        $this->pruneDeletableGroups();

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_update_group(): void
    {
        exec('sudo groupadd -g 334 guestupdgrp');
        $this->addDeletableGroup('guestupdgrp');

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
        $this->addDeletableGroup('updatetestgroup');

        $response = $this->authed()->putJson($this->endpoint($group['gid']), [
            'name' => 'updatetestgroup-renamed',
        ]);
        $this->addDeletableGroup('updatetestgroup-renamed');

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'updatetestgroup-renamed']);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_update_group_gid(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'gidtestgroup',
        ])->json();
        $this->addDeletableGroup('gidtestgroup');

        $response = $this->authed()->putJson($this->endpoint($group['gid']), [
            'name' => 'gidtestgroup',
            'gid' => (int) $group['gid'] + 1,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['gid' => (int) $group['gid'] + 1]);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_remove_all_users_from_group(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'groupwithmembers',
        ]);
        $this->addDeletableGroup('groupwithmembers');

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

    /** @test */
    public function cannot_add_nonexistent_users_to_a_group(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'godfather',
        ]);
        $this->addDeletableGroup('godfather');

        $response = $this->authed()->putJson($this->endpoint($group->json()['gid']), [
            'name' => 'godfather',
            'users' => ['ghost'],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['gid']);
        $response->assertJsonFragment(['gid' => ["Couldn't update the group's users. Exit code: 3."]]);
    }

    /** @test */
    public function updating_a_group_should_fail_if_gid_exists(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'renamefail',
        ]);
        $this->addDeletableGroup('renamefail');
        $data = $group->json();

        $data['gid'] = 3;

        $response = $this->authed()->putJson($this->endpoint($group->json()['gid']), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['gid']);
        $response->assertJsonFragment(['gid' => ["Couldn't update the group. Exit code: 4."]]);
    }

    /** @test */
    public function updating_a_group_without_changes_should_fail(): void
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'changeless',
        ]);
        $this->addDeletableGroup('changeless');

        $response = $this->authed()->putJson(
            $this->endpoint($group->json()['gid']),
            $group->json(),
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['gid']);
        $response->assertJsonFragment(['gid' => ['Nothing to update!']]);
    }
}
