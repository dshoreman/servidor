<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSystemGroupsTest extends TestCase
{
    /** @test */
    public function canViewGroupsList()
    {
        $response = $this->getJson('/api/system/groups');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root',
        ]);
    }

    /** @test */
    public function listIsAnArray()
    {
        $response = $this->getJson('/api/system/groups');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }
}
