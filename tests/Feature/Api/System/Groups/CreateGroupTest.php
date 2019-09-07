<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class CreateGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    public function tearDown()
    {
        $this->pruneDeletable('groups');

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_create_group()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'guesttestgroup',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonCount(1);
        $response->assertJson(['message' => 'Unauthenticated.']);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['name' => 'guesttestgroup']);
    }

    /** @test */
    public function authed_user_can_create_user_with_minimum_data()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'newtestgroup',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestgroup']);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('group', $response);
    }

    /** @test */
    public function cannot_create_group_without_required_fields()
    {
        $response = $this->authed()->postJson($this->endpoint, ['gid' => 1337]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['gid' > 1337]);
    }

    /** @test */
    public function cannot_create_group_with_invalid_data()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '',
            'users' => 'notanarray',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'users',
        ]);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['users' => 'notanarray']);
    }

    /** @test */
    public function name_cannot_start_with_dash()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_plus()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_tilde()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_contain_colon()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function name_cannot_contain_comma()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test,contains,comma',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function name_cannot_contain_tab()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\tcontains\ttab",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_newline()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\ncontains\nnewline",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_whitespace()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test contains space',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_ending_with_whitespace_gets_trimmed()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'testgroup ',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testgroup']);

        $this->addDeletable('group', $response);
    }

    /** @test */
    public function name_cannot_be_too_long()
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '_im-a-name-that-is-over-32-chars-',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }
}
