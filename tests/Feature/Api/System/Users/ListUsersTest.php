<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\RequiresAuth;

class ListUsersTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function can_view_users_page()
    {
        $response = $this->authed()->getJson($this->endpoint);

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
    public function list_response_contains_expected_data($response)
    {
        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));

        $response->assertJsonStructure([$this->expectedKeys]);
    }
}
