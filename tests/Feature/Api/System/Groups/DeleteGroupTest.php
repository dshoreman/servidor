<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class DeleteGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown(): void
    {
        $this->pruneDeletableGroups();

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_delete_group(): void
    {
        exec('sudo groupadd -g 333 guestdeletetest');
        $this->addDeletableGroup('guestdeletetest');

        $response = $this->deleteJson($this->endpoint(333));

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);

        $search = $this->authed()->getJson($this->endpoint);
        $search->assertOk();
        $search->assertJsonFragment(['name' => 'guestdeletetest']);
    }

    /** @test */
    public function authed_user_can_delete_group()
    {
        $group = $this->authed()->postJson($this->endpoint, [
            'name' => 'deletetestgroup',
        ])->json();

        $response = $this->authed()->deleteJson($this->endpoint($group['gid']), []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $group;
    }

    /**
     * @test
     *
     * @depends authed_user_can_delete_group
     */
    public function group_does_not_exist_after_deletion($group): void
    {
        $response = $this->authed()->getJson($this->endpoint($group['gid']));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
