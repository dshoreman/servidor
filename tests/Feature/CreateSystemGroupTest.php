<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSystemGroupTest extends TestCase
{
    public function canCreateWithMinimumData()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'newtestgroup',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function nameCannotStartWithDash()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '-test-dash-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotStartWithPlus()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '+test-plus-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotStartWithTilde()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '~test-tilde-prefix',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotContainColon()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test-contains-:',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotContainComma()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test-contains-,',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotContainTab()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => "test-contains-\t",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotContainNewline()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => "test-contains-\n",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotContainWhitespace()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => 'test-contains- ',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function nameCannotBeTooLong()
    {
        $response = $this->postJson('/api/system/groups', [
            'name' => '_im-a-name-that-is-over-32-chars',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
