<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_view_users_page()
    {
        $response = $this->authed()->getJson('/api/system/users');

        $response->assertStatus(Response::HTTP_OK);

        return $response;
    }

    /**
     * @test
     * @depends can_view_users_page
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
     * @depends can_view_users_page
     */
    public function list_includes_system_users($response)
    {
        $response->assertJsonFragment(['name' => 'root']);
    }

    /**
     * @test
     * @depends can_view_users_page
     */
    public function list_is_an_array($response)
    {
        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }

    /**
     * @test
     * @depends can_view_users_page
     */
    public function list_results_contain_expected_data($response)
    {
        $response->assertJsonStructure([[
            'name',
            'passwd',
            'uid',
            'gid',
            'groups',
            'gecos',
            'dir',
            'shell',
        ]]);
    }
}
