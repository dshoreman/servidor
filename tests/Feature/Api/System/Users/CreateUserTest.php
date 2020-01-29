<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Http\Response;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;

class CreateUserTest extends TestCase
{
    use PrunesDeletables;
    use RequiresAuth;

    public function tearDown(): void
    {
        $this->pruneDeletable('users');

        parent::tearDown();
    }

    /** @test */
    public function guest_cannot_create_user(): void
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'guesttestuser',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonCount(1);
        $response->assertJson(['message' => 'Unauthenticated.']);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['name' => 'guesttestuser']);
    }

    /** @test */
    public function authed_user_can_create_user_with_minimum_data(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'newtestuser',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestuser']);
        $response->assertJsonStructure($this->expectedKeys);

        $this->addDeletable('user', $response);
    }

    /** @test */
    public function can_create_user_with_custom_gid(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'customgid',
            'gid' => 1,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($this->expectedKeys);
        $response->assertJsonFragment([
            'name' => 'customgid',
            'gid' => 1,
        ]);

        $this->addDeletable('user', $response);
    }

    /** @test */
    public function cannot_create_user_without_required_fields(): void
    {
        $response = $this->authed()->postJson($this->endpoint, ['uid' => 1337]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name', 'gid']);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['uid' > 1337]);
    }

    /** @test */
    public function cannot_create_user_with_existing_name(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'bin',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'name' => 'bin',
            'user_group' => true,
            'error' => 'Something went wrong (exit code: 9)',
        ]);
    }

    /** @test */
    public function cannot_create_user_with_invalid_data(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '',
            'gid' => '',
            'create_home' => 'foo',
            'user_group' => 'bar',
            'groups' => 'notanarray',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'gid',
            'create_home',
            'user_group',
        ]);

        $updated = $this->authed()->getJson($this->endpoint);
        $updated->assertJsonMissing(['groups' => 'notanarray']);
    }

    /** @test */
    public function name_cannot_start_with_dash(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '-test-dash-prefix',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_plus(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '+test-plus-prefix',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_tilde(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '~test-tilde-prefix',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_contain_colon(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test-contains-:',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function name_cannot_contain_comma(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test,contains,comma',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function name_cannot_contain_tab(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\tcontains\ttab",
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_newline(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => "test\ncontains\nnewline",
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_whitespace(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'test contains space',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_ending_with_whitespace_gets_trimmed(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => 'testuser ',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testuser']);

        $this->addDeletable('user', $response);
    }

    /** @test */
    public function name_cannot_be_too_long(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '_im-a-name-that-is-over-32-chars-',
            'user_group' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }
}
