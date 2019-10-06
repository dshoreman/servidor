<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class ListUsersTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    protected function tearDown()
    {
        $this->pruneDeletable('users');

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_list_users()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_users()
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();

        return $response;
    }

    /**
     * @test
     * @depends authed_user_can_list_users
     */
    public function list_includes_normal_users($response)
    {
        // Vagrant has a vagrant (and ubuntu) user but PHP could be running as
        // www-data instead, so we use the SUDO_USER value that it also sets.
        $response->assertJsonFragment([
            'name' => getenv('TRAVIS') ? 'travis' : getenv('SUDO_USER') ?: exec('whoami'),
        ]);
    }

    /**
     * @test
     * @depends authed_user_can_list_users
     */
    public function list_includes_system_users($response)
    {
        $response->assertJsonFragment(['name' => 'root']);
    }

    /** @test */
    public function listed_user_groups_should_not_include_colon()
    {
        $user = $this->authed()->postJson($this->endpoint, [
            'name' => 'nocolon',
            'gid' => 0,
        ])->json();

        $user = $this->authed()->putJson(
            $this->endpoint($user['uid']),
            array_merge($user, ['groups' => ['games']]),
        );

        $response = $this->authed()->getJson($this->endpoint);
        $json = $response->json();

        $updated = end($json);
        $this->addDeletable('user', (int) $updated['uid']);

        $response->assertOk();
        $this->assertArraySubset(['groups' => ['games']], $updated);
        $this->assertNotContains(':', $updated['groups']);
    }

    /**
     * @test
     * @depends authed_user_can_list_users
     */
    public function list_response_contains_expected_data($response)
    {
        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));

        $response->assertJsonStructure([$this->expectedKeys]);
    }
}
