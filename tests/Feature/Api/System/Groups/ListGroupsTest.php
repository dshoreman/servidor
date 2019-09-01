<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListGroupsTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function can_view_groups_page()
    {
        $response = $this->authed()->getJson('/api/system/groups');

        $response->assertStatus(Response::HTTP_OK);

        return $response;
    }

    /**
     * @test
     * @depends can_view_groups_page
     */
    public function list_is_an_array($response)
    {
        $responseJson = json_decode($response->getContent());

        $this->assertIsArray($responseJson);
    }

    /**
     * @test
     * @depends can_view_groups_page
     */
    public function list_results_contain_expected_data($response)
    {
        $response->assertJsonStructure([[
            'gid',
            'name',
            'password',
            'users',
        ]]);
    }

    /**
     * @test
     * @depends can_view_groups_page
     */
    public function list_results_include_default_groups($response)
    {
        $response->assertJsonFragment(['name' => 'root']);
        $response->assertJsonFragment(['name' => 'users']);
    }
}
