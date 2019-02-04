<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequiresAuth;

class UpdateSystemGroupTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function canUpdateGroup()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'updatetest',
        ])->json();

        $response = $this->authed()->putJson('/api/system/groups/'.$group['gid'], [
            'name' => 'renametest',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'name',
            'gid',
            'users',
        ]);
    }

    /** @test */
    public function cannotUpdateNonExistantGroup()
    {
        $response = $this->authed()->putJson('/api/system/groups/9032', [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
