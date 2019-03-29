<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequiresAuth;

class SystemGroupsTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /**
     * @var array
     */
    private $deleteGroupIds = [];

    public function tearDown()
    {
        $this->deleteTemporaryGroups();
    }

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
        $response->assertJsonStructure($this->expectedKeys());
    }

    /**
     * @test
     * @depends canCreateWithMinimumData
     */
    public function canUpdateGroup($response)
    {
        $group = $response->json();

        $response = $this->authed()->putJson('/api/system/groups/'.$group['gid'], [
            'name' => 'newtestgroup-renamed',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => 'newtestgroup-renamed']);

        return $response;
    }

    /**
     * @test
     * @depends canUpdateGroup
     */
    public function updateResponseContainsAllKeys($response)
    {
        $response->assertJsonStructure($this->expectedKeys());
    }

    /** @test */
    public function cannotUpdateNonExistantGroup()
    {
        $response = $this->authed()->putJson('/api/system/groups/9032', [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @depends canUpdateGroup
     */
    public function canDeleteGroup($response)
    {
        $group = $response->json();

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

        $this->addToDeleteList($response);
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

    private function expectedKeys()
    {
        return [
            'gid',
            'name',
            'users',
        ];
    }

    private function addToDeleteList($response)
    {
        $group = $response->json();

        $this->deleteGroupIds[] = $group['gid'];
    }

    private function deleteTemporaryGroups()
    {
        $endpoint = '/api/system/groups/';

        foreach ($this->deleteGroupIds as $gid) {
            $this->authed()->deleteJson($endpoint.$gid, []);
        }
    }
}
