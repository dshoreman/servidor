<?php

namespace Tests\Feature;

use Servidor\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSystemGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canDeleteGroup()
    {
        $user = factory(User::class)->create();

        $group = $this->actingAs($user, 'api')
            ->postJson('/api/system/groups', [
                'name' => 'delete-test',
            ])->json();

        $response = $this->actingAs($user, 'api')
                         ->deleteJson('/api/system/groups/'.$group['gid'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
