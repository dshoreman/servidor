<?php

namespace Tests\Feature\Api;

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

        $first = $response->json()[0];
        $this->assertIsInt($first['tableCount']);
        $this->assertEquals('utf8mb4', $first['charset']);
        $this->assertStringContainsString('general_ci', $first['collation']);
    }
}
