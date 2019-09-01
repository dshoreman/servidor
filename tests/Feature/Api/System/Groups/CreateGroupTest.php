<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreateGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    public function tearDown()
    {
        $this->pruneDeletable('groups');
    }

    /** @test */
    public function can_create_with_minimum_data()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'newtestgroup',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestgroup']);

        $this->addDeletable('group', $response);

        return $response;
    }

    /**
     * @test
     * @depends can_create_with_minimum_data
     */
    public function create_response_contains_all_keys($response)
    {
        $response->assertJsonStructure($this->expectedKeys());
    }

    /** @test */
    public function name_cannot_start_with_dash()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_plus()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_tilde()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_contain_colon()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function name_cannot_contain_comma()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test,contains,comma',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function name_cannot_contain_tab()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => "test\tcontains\ttab",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_newline()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => "test\ncontains\nnewline",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_whitespace()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test contains space',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_ending_with_whitespace_gets_trimmed()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'testgroup ',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testgroup']);

        $this->addDeletable('group', $response);
    }

    /** @test */
    public function name_cannot_be_too_long()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '_im-a-name-that-is-over-32-chars-',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }

    private function expectedKeys()
    {
        return [
            'gid',
            'name',
            'users',
        ];
    }
}
