<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class CreateGroupTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    public function tearDown(): void
    {
        $this->pruneDeletableGroups();

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_create_group(): void
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
    public function authed_user_can_create_group_with_minimum_data(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'newtestgroup',
        ]);
        $this->addDeletableGroup('newtestgroup');

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestgroup']);
        $response->assertJsonStructure($this->expectedKeys);
    }

    /** @test */
    public function authed_user_can_create_system_group(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'systemsal',
            'system' => true,
        ]);
        $this->addDeletableGroup('systemsal');

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($this->expectedKeys);
        $this->assertLessThan(1000, $response->json()['gid']);
    }

    /** @test */
    public function cannot_create_group_without_required_fields(): void
    {
        $response = $this->authed()->postJson($this->endpoint, ['gid' => 1337]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['gid' => 1337]);
    }

    /** @test */
    public function cannot_create_group_with_existing_name(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'bin',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'name' => 'bin',
            'error' => 'The group name must be unique',
        ]);
    }

    /** @test */
    public function cannot_create_group_with_existing_gid(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'imposter',
            'gid' => 3,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'name' => 'imposter',
            'error' => "The group's GID must be unique",
        ]);
    }

    /** @test */
    public function cannot_create_group_with_invalid_data(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
        ]);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['users' => 'notanarray']);
    }

    /** @test */
    public function name_cannot_start_with_dash(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_plus(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_tilde(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_contain_colon(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function name_cannot_contain_comma(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test,contains,comma',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function name_cannot_contain_tab(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\tcontains\ttab",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_newline(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\ncontains\nnewline",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_whitespace(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test contains space',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_ending_with_whitespace_gets_trimmed(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'testgroup ',
        ]);
        $this->addDeletableGroup('testgroup');

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testgroup']);
    }

    /** @test */
    public function name_cannot_be_too_long(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '_im-a-name-that-is-over-32-chars-',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name must not be greater than 32 characters.']);
    }
}
