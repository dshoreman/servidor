<?php

namespace Tests\Feature\Api\System\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /**
     * @var array
     */
    private $deleteUserIds = [];

    public function tearDown()
    {
        $this->deleteTemporaryUsers();

        parent::tearDown();
    }

    /** @test */
    public function can_create_with_minimum_data()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => 'newtestuser',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestuser']);

        $this->addToDeleteList($response);

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
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => '-test-dash-prefix',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_plus()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => '+test-plus-prefix',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_start_with_tilde()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => '~test-tilde-prefix',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function name_cannot_contain_colon()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => 'test-contains-:',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function name_cannot_contain_comma()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => 'test,contains,comma',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function name_cannot_contain_tab()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => "test\tcontains\ttab",
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_newline()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => "test\ncontains\nnewline",
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_cannot_contain_whitespace()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => 'test contains space',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function name_ending_with_whitespace_gets_trimmed()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => 'testuser ',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testuser']);

        $this->addToDeleteList($response);
    }

    /** @test */
    public function name_cannot_be_too_long()
    {
        $response = $this->authed()->postJson('/api/system/users', [
            'name' => '_im-a-name-that-is-over-32-chars-',
            'gid' => 0,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }

    private function addToDeleteList($response)
    {
        $user = $response->json();

        $this->deleteUserIds[] = $user['gid'];
    }

    private function deleteTemporaryUsers()
    {
        $endpoint = '/api/system/users/';

        foreach ($this->deleteUserIds as $gid) {
            $this->authed()->deleteJson($endpoint.$gid, []);
        }
    }

    private function expectedKeys()
    {
        return [
            'name',
            'passwd',
            'uid',
            'gid',
            'gecos',
            'dir',
            'shell',
        ];
    }
}
