<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class UpdateUserTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown(): void
    {
        $this->pruneDeletableUsers();
        $this->pruneDeletableGroups();

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_update_user(): void
    {
        exec('sudo useradd -u 4270 guestupduser');
        $this->addDeletableUser('guestupduser');

        $response = $this->putJson($this->endpoint(4270), [
            'name' => 'guestupdateduser',
            'user_group' => true,
        ]);

        $updated = $this->authed()->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonCount(1);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $updated->assertJsonFragment(['name' => 'guestupduser']);
    }

    /** @test */
    public function authed_user_can_update_user(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'updatetestuser',
            'user_group' => true,
        ])->json();
        $this->addDeletableUser('updatetestuser', true);
        $this->addDeletableGroup('updatetestuser');

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => 'updatetestuser-renamed',
        ]);
        $this->addDeletableUser('updatetestuser-renamed', true);

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'updatetestuser-renamed']);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_change_a_users_uid(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'uidchanger',
            'user_group' => true,
        ])->json();
        $this->addDeletableUser('uidchanger');

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => $user['name'],
            'uid' => $user['uid'] + 1,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['uid' => $user['uid'] + 1]);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_change_a_users_home_directory(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'dirchanger',
            'user_group' => true,
        ])->json();
        $this->addDeletableUser('dirchanger');

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'dir' => '/home/changeddir',
            'name' => $user['name'],
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['dir' => '/home/changeddir']);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_change_a_users_shell(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'shelly-mk-ii',
            'user_group' => true,
        ]);
        $this->addDeletableUser('shelly-mk-ii');
        $user->assertStatus(Response::HTTP_CREATED);

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => $user['name'],
            'shell' => '/bin/zsh',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['shell' => '/bin/zsh']);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function cannot_update_nonexistant_user(): void
    {
        $response = $this->authed()->putJson($this->endpoint(9032), [
            'name' => 'nousertest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function updating_a_user_without_changes_should_fail(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'changeless',
            'user_group' => true,
        ]);
        $this->addDeletableUser('changeless');

        $response = $this->authed()->putJson(
            $this->endpoint($user->json()['uid']),
            $user->json(),
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['uid']);
        $response->assertJsonFragment(['uid' => ['Nothing to update!']]);
    }

    /**
     * @test
     * @group issue154
     */
    public function group_should_be_set_after_updating_a_user(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'userhasgroups',
            'user_group' => true,
        ])->json();
        $this->addDeletableUser('userhasgroups');

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => 'userhasgroups',
            'groups' => ['adm'],
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['groups' => ['adm']]);
        $response->assertJsonStructure($this->expectedKeys);
    }
}
