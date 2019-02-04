<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequiresAuth;

class CreateSystemGroupTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function canViewGroupsList()
    {
        $response = $this->authed()->getJson('/api/system/groups');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root',
        ]);
    }

    /** @test */
    public function listIsAnArray()
    {
        $response = $this->authed()->getJson('/api/system/groups');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }

    /** @test */
    public function canCreateWithMinimumData()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'newtestgroup',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestgroup']);

        return $response;
    }

    /**
     * @test
     * @depends canCreateWithMinimumData
     */
    public function createResponseContainsAllKeys($response)
    {
        $response->assertJsonStructure([
            'gid',
            'name',
            'users',
        ]);
    }

    /** @test */
    public function canUpdateGroup()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'updatetest',
        ])->json();

        $response = $this->authed()->putJson('/api/system/groups/'.$group['gid'], [
            'name' => 'renametest',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'name',
            'gid',
            'users',
        ]);
    }

    /** @test */
    public function cannotUpdateNonExistantGroup()
    {
        $response = $this->authed()->putJson('/api/system/groups/9032', [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function canDeleteGroup()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'delete-test',
        ])->json();

        $response = $this->authed()->deleteJson('/api/system/groups/'.$group['gid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function nameCannotStartWithDash()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotStartWithPlus()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotStartWithTilde()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotContainColon()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function nameCannotContainComma()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test,contains,comma',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function nameCannotContainTab()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => "test\tcontains\ttab",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameCannotContainNewline()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => "test\ncontains\nnewline",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameCannotContainWhitespace()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'test contains space',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameEndingWithWhitespaceGetsTrimmed()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => 'testgroup ',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'testgroup']);
    }

    /** @test */
    public function nameCannotBeTooLong()
    {
        $response = $this->authed()->postJson('/api/system/groups', [
            'name' => '_im-a-name-that-is-over-32-chars-',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }
}
