<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreatePathTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files';

    /** @test */
    public function guest_cannot_create_a_file(): void
    {
        $file = resource_path('test-skel/new.md');

        $response = $this->postJson($this->endpoint(['file' => $file]), [
            'contents' => '# New File',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertFileNotExists($file);
    }

    /** @test */
    public function authed_user_can_create_a_file(): void
    {
        $file = resource_path('test-skel/created.txt');

        $response = $this->authed()->postJson($this->endpoint(), [
            'file' => $file,
            'contents' => '# New File',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertFileExists($file);

        unlink($file);
    }

    /** @test */
    public function authed_user_can_create_a_folder(): void
    {
        $this->withoutExceptionHandling();
        $dir = resource_path('test-skel/newdir');

        $response = $this->authed()->postJson($this->endpoint(), [
            'dir' => $dir,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDirectoryExists($dir);

        rmdir($dir);
    }

    /** @test */
    public function cannot_create_folder_if_target_exists(): void
    {
        $dir = resource_path('test-skel/mixed');

        $response = $this->authed()->postJson($this->endpoint(), [
            'dir' => $dir,
        ]);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
