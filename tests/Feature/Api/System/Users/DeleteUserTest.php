<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\FileManager\FileManager;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class DeleteUserTest extends TestCase
{
    use PrunesDeletables;
    use RefreshDatabase;
    use RequiresAuth;

    protected function tearDown(): void
    {
        $this->pruneDeletableUsers();

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_delete_user(): void
    {
        exec('sudo useradd -u 4269 guestdeleteuser');
        $this->addDeletableUser('guestdeleteuser');

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
            'user_group' => true,
        ])->json();

        $response = $this->authed()->deleteJson($this->endpoint($user['uid']), []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $user;
    }

    /**
     * @test
     * @depends authed_user_can_delete_user
     */
    public function user_does_not_exist_after_deletion($user): void
    {
        $response = $this->authed()->getJson($this->endpoint($user['uid']));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function user_home_directory_can_be_purged(): void
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'userwithhomedir',
            'create_home' => true,
            'user_group' => true,
        ])->json();

        $before = (new FileManager())->list('/home');
        $response = $this->authed()->deleteJson($this->endpoint($user['uid']), [
            'deleteHome' => true,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $after = (new FileManager())->list('/home');
        $this->assertEquals(\count($before) - 1, \count($after));
    }
}
