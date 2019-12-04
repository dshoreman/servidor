<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\RequiresAuth;

class ListGroupsTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function guest_cannot_list_groups(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_groups()
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();

        return $response;
    }

    /**
     * @test
     * @depends authed_user_can_list_groups
     */
    public function list_response_contains_expected_data($response): void
    {
        $responseJson = json_decode($response->getContent());

        $this->assertIsArray($responseJson);

        $response->assertJsonStructure([$this->expectedKeys]);
    }

    /**
     * @test
     * @depends authed_user_can_list_groups
     */
    public function list_results_include_default_groups($response): void
    {
        $response->assertJsonFragment(['name' => 'root']);
        $response->assertJsonFragment(['name' => 'users']);
    }
}
