<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSystemGroupTest extends TestCase
{
    /** @test */
    public function canCreateWithMinimumData()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'newtestgroup',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['name' => 'newtestgroup']);
    }

    /** @test */
    public function nameCannotStartWithDash()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotStartWithPlus()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotStartWithTilde()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name format is invalid.']);
    }

    /** @test */
    public function nameCannotContainColon()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a colon.']);
    }

    /** @test */
    public function nameCannotContainComma()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test,contains,comma',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain a comma.']);
    }

    /** @test */
    public function nameCannotContainTab()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => "test\tcontains\ttab",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameCannotContainNewline()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => "test\ncontains\nnewline",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameCannotContainWhitespace()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test contains space',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name cannot contain whitespace or newlines.']);
    }

    /** @test */
    public function nameCannotBeTooLong()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '_im-a-name-that-is-over-32-chars-',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['The name may not be greater than 32 characters.']);
    }
}
