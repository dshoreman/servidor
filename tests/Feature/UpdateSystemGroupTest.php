<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateSystemGroupTest extends TestCase
{
    /** @test */
    public function canUpdateGroup()
    {
        $group = $this->postJson('/api/system/groups', [
            'name' => 'updatetest',
        ])->json();

        $response = $this->putJson('/api/system/groups/'.$group['id'], [
            'name' => 'renametest',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'name',
            'id',
            'users',
        ]);
    }

    /** @test */
    public function cannotUpdateNonExistantGroup()
    {
        $response = $this->putJson('/api/system/groups/9032', [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
