<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class DeletePathTest extends TestCase
{
    use RequiresAuth;

    protected string $endpoint = '/api/files';

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
        $this->assertFileDoesNotExist($path);
    }

    /** @test */
    public function authed_user_can_delete_a_directory(): void
    {
        $path = resource_path('test-skel/deletable-dir');
        is_dir($path) || mkdir($path, 0777);

        $response = $this->authed()->deleteJson($this->endpoint(['file' => $path]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertFileDoesNotExist($path);
    }

    /** @test */
    public function delete_returns_no_content_response_when_file_does_not_exist(): void
    {
        $response = $this->authed()->deleteJson($this->endpoint([
            'file' => resource_path('test-skel/non-existant.md'),
        ]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function delete_throws_error_when_file_is_not_given(): void
    {
        $response = $this->authed()->deleteJson($this->endpoint(['foo' => 'bar']));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['file' => 'File path must be specified.']);
    }

    /** @test */
    public function deleting_unwritable_files_is_forbidden(): void
    {
        $response = $this->authed()->deleteJson($this->endpoint([
            'file' => resource_path('test-skel/protected/forbidden'),
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
