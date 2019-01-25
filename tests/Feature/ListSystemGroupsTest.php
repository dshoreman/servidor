<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSystemGroupsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canViewGroupsList()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/system/groups');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root',
        ]);
    }

    /** @test */
    public function listIsAnArray()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/system/groups');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }
}
