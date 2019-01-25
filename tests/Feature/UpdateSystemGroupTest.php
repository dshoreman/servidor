<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateSystemGroupTest extends TestCase
{
    /** @test */
    public function canUpdateGroup()
    {
        $user = factory(User::class)->create();

        $group = $this->actingAs($user, 'api')
            ->postJson('/api/system/groups', [
                'name' => 'updatetest',
            ])->json();

        $response = $this->actingAs($user, 'api')
            ->putJson('/api/system/groups/'.$group['gid'], [
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
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')
            ->putJson('/api/system/groups/9032', [
                'name' => 'nogrouptest',
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
