<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSystemUsersTest extends TestCase
{
    /** @test */
    public function listIncludesSystemUsers()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/system/users');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'nobody',
        ]);
    }

    /** @test */
    public function listIncludesNormalUsers()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/system/users');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root'
        ]);
    }

    /**
     * @test
     */
    public function listIsAnArray()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/system/users');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }
}
