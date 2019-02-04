<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequiresAuth;

class DeleteSystemGroupTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function canDeleteGroup()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'delete-test',
        ])->json();

        $response = $this->authed()->deleteJson('/api/system/groups/'.$group['gid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
