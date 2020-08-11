<?php

namespace Tests\Feature\Api;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/databases';

    /** @test */
    public function guest_cannot_list_databases(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_databases(): void
    {
        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();
        $response->assertJsonFragment(['information_schema']);
        $response->assertJsonFragment(['performance_schema']);
        $response->assertJsonFragment(['mysql']);
    }

    /** @test */
    public function authed_user_can_create_a_database(): void
    {
        $db = 'caniplz';
        $response = $this->authed()->postJson($this->endpoint, ['database' => $db]);

        $response->assertOk();
        $this->assertSame($db, $response->json());
    }

    /** @test */
    public function creation_fails_when_name_is_invalid(): void
    {
        $db = 'some_really_long_name_that_is_so_long_it_really';
        $db .= '_should_be_split_over_multiple_lines';
        $response = $this->authed()->postJson($this->endpoint, ['database' => $db]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertExactJson(['error' => 'Could not create database']);
    }
}
