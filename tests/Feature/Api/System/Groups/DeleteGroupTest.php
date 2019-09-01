<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DeleteGroupTest extends TestCase
{
    use RequiresAuth;

    /** @test */
    public function can_delete_group()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'deletetestgroup',
        ])->json();

        $response = $this->authed()->deleteJson('/api/system/groups/'.$group['gid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        return $group;
    }

    /**
     * @test
     * @depends can_delete_group
     */
    public function group_does_not_exist_after_deletion($group)
    {
        $response = $this->authed()->getJson('/api/system/groups/'.$group['gid']);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
