<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DeletePathTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files';

    /** @test */
    public function guest_cannot_delete_a_file(): void
    {
        $file = resource_path('test-skel/guest/nodelete.txt');
        $response = $this->deleteJson($this->endpoint(['file' => $file]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function authed_user_can_delete_a_file(): void
    {
        $path = resource_path('test-skel/deletable.txt');
        file_put_contents($path, 'self-destructing');

        $response = $this->authed()->deleteJson($this->endpoint(['file' => $path]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertFileNotExists($path);
    }
}
