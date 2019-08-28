<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DeleteGroupTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_delete_group()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'deletetestgroup',
        ])->json();

        $response = $this->authed()->deleteJson('/api/system/groups/'.$group['gid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
