<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteSystemGroupTest extends TestCase
{
    /** @test */
    public function canDeleteGroup()
    {
        $group = $this->postJson('/api/system/groups', [
            'name' => 'delete-test',
        ])->json();

        $response = $this->deleteJson('/api/system/groups/'.$group['id'], []);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
