<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\RequiresAuth;

class ListGroupsTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function can_view_groups_page()
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);

        return $response;
    }

    /**
     * @test
     * @depends can_view_groups_page
     */
    public function list_response_contains_expected_data($response)
    {
        $responseJson = json_decode($response->getContent());

        $this->assertIsArray($responseJson);

        $response->assertJsonStructure([$this->expectedKeys]);
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
