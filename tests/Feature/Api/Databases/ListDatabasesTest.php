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
        $response->assertJsonFragment(['information_schema']);
        $response->assertJsonFragment(['performance_schema']);
        $response->assertJsonFragment(['mysql']);
    }

    /** @test */
    public function cannot_list_databases_as_guest(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }
}
