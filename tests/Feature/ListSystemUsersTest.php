<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListSystemUsersTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function listIncludesSystemUsers()
    {
        $response = $this->authed()->getJson('/api/system/users');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'nobody',
        ]);
    }

    /** @test */
    public function listIncludesNormalUsers()
    {
        $response = $this->authed()->getJson('/api/system/users');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root',
        ]);
    }

    /**
     * @test
     */
    public function listIsAnArray()
    {
        $response = $this->authed()->getJson('/api/system/users');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }
}
