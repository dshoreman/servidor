<?php

namespace Tests\Feature\Api;

use Illuminate\Http\JsonResponse;
use Tests\RequiresAuth;
use Tests\TestCase;

class NewDatabaseTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/databases';

    /** @test */
    public function can_create_a_database(): void
    {
        $data = ['database' => 'caniplz'];
        $response = $this->authed()->postJson($this->endpoint, $data);

        $response->assertOk();
        $this->assertSame(['name' => 'caniplz'], $response->json());
    }

    /** @test */
    public function cannot_create_as_guest(): void
    {
        $response = $this->postJson($this->endpoint, ['database' => 'denied']);

        $response->assertUnauthorized();
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function creation_fails_when_name_is_invalid(): void
    {
        $db = 'some_really_long_name_that_is_so_long_it_really';
        $db .= '_should_be_split_over_multiple_lines';
        $response = $this->authed()->postJson($this->endpoint, ['database' => $db]);

        $response->assertStatus(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertExactJson(['error' => 'Could not create database']);
    }
}
