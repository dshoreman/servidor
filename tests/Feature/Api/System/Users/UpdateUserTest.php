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
        $this->pruneDeletable(['users', 'groups']);

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_update_user(): void
    {
        exec('sudo useradd -u 4270 guestupduser');
        $this->addDeletable('user', 4270);

        $response = $this->putJson($this->endpoint(4270), [
            'name' => 'guestupdateduser',
            'gid' => 0,
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
            'gid' => 0,
        ])->json();

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => 'updatetestuser-renamed',
            'gid' => 0,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'updatetestuser-renamed']);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('user', $response);
        $this->addDeletable('group', $response);
    }

    /** @test */
    public function cannot_update_nonexistant_user(): void
    {
        $response = $this->authed()->putJson($this->endpoint(9032), [
            'name' => 'nousertest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
            'gid' => 0,
        ])->json();

        $response = $this->authed()->putJson($this->endpoint($user['uid']), [
            'name' => 'userhasgroups',
            'gid' => 0,
            'groups' => ['adm'],
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['groups' => ['adm']]);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('user', $response);
        $this->addDeletable('group', $response);
    }
}
