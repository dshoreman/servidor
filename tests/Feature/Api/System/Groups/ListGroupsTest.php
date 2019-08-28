<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListGroupsTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_view_groups_list()
    {
        $response = $this->authed()->getJson('/api/system/groups');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'name' => 'root',
        ]);
    }

    /** @test */
    public function list_is_an_array()
    {
        $response = $this->authed()->getJson('/api/system/groups');

        $responseJson = json_decode($response->getContent());

        $this->assertEquals('array', gettype($responseJson));
    }

    private function expectedKeys()
    {
        return [
            'gid',
            'name',
            'users',
        ];
    }
}
