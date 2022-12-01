<?php

namespace Tests\Feature\Api\Databases;

use Tests\RequiresAuth;
use Tests\TestCase;

class ListDatabasesTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/databases';

    /** @test */
    public function can_list_databases(): void
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'information_schema']);
        $response->assertJsonFragment(['name' => 'performance_schema']);
        $response->assertJsonFragment(['name' => 'mysql']);
    }

    /** @test */
    public function cannot_list_databases_as_guest(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function list_includes_details(): void
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();
        $response->assertJsonStructure([['name', 'charset', 'collation', 'tableCount']]);

        $json = $response->json();
        $result = $json[array_search('information_schema', array_column($json, 'name'), true)];

        $this->assertIsInt($result['tableCount']);
        $this->assertContains($result['charset'], ['utf8', 'utf8mb3', 'utf8mb4']);
        $this->assertContains($result['collation'], ['utf8_general_ci', 'utf8mb3_general_ci', 'utf8mb4_0900_ai_ci']);
    }
}
