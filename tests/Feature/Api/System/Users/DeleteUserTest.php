<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class DeleteUserTest extends TestCase
{
    use PrunesDeletables;
    use RefreshDatabase;
    use RequiresAuth;

    protected function tearDown()
    {
        $this->pruneDeletable('users');

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_delete_user()
    {
        exec('sudo useradd -u 4269 guestdeleteuser');
        $this->addDeletable('user', 4269);

        $endpoint = $this->endpoint(4269);
        $response = $this->deleteJson($this->endpoint(4269));

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);

        $search = $this->authed()->getJson($this->endpoint);
        $search->assertOk();
        $search->assertJsonFragment(['name' => 'guestdeleteuser']);
    }

    /** @test */
    public function authed_user_can_delete_user(): array
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'deletetestuser',
            'gid' => 0,
        ])->json();

        $response = $this->authed()->deleteJson($this->endpoint($user['uid']), []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $user;
    }

    /**
     * @test
     * @depends authed_user_can_delete_user
     */
    public function user_does_not_exist_after_deletion($user)
    {
        $response = $this->authed()->getJson($this->endpoint($user['uid']));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
