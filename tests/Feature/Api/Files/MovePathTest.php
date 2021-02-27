<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class MovePathTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files/rename';

    /** @test */
    public function guest_cannot_rename_a_file(): void
    {
        $file = resource_path('test-skel/guest.txt');
        $newFile = $file . '.moved';

        $response = $this->postJson($this->endpoint(), [
            'oldPath' => $file,
            'newPath' => $newFile,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertFileExists($file);
        $this->assertFileDoesNotExist($newFile);
    }

    /** @test */
    public function authed_user_can_rename_a_file(): void
    {
        $file = resource_path('test-skel/moveme.txt');
        $newFile = $file . '.moved';
        file_put_contents($file, 'temp');

        $response = $this->authed()->postJson($this->endpoint(), [
            'oldPath' => $file,
            'newPath' => $newFile,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertFileDoesNotExist($file);
        $this->assertFileExists($newFile);
        $this->assertStringEqualsFile($newFile, 'temp');

        unlink($newFile);
    }

    /** @test */
    public function can_rename_a_directory(): void
    {
        $this->withoutExceptionHandling();
        $dir = resource_path('test-skel/moveme');
        $newDir = $dir . '.moved';
        is_dir($dir) || mkdir($dir, 0777);

        $response = $this->authed()->postJson($this->endpoint(), [
            'oldPath' => $dir,
            'newPath' => $newDir,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDirectoryDoesNotExist($dir);
        $this->assertDirectoryExists($newDir);

        rmdir($newDir);
    }

    /** @test */
    public function cannot_rename_file_if_target_exists(): void
    {
        $file = resource_path('test-skel/editme.txt');
        $newFile = resource_path('test-skel/existing.txt');

        $response = $this->authed()->postJson($this->endpoint(), [
            'oldPath' => $file,
            'newPath' => $newFile,
        ]);

        $response->assertStatus(Response::HTTP_CONFLICT);
        $this->assertFileExists($file);
        $this->assertFileExists($newFile);
        $this->assertStringEqualsFile($newFile, "I'm a rename target that already exists!\n");
    }

    /** @test */
    public function cannot_rename_non_existent_file(): void
    {
        $file = resource_path('test-skel/not-here');
        $newFile = $file . '.moved';

        $response = $this->authed()->postJson($this->endpoint(), [
            'oldPath' => $file,
            'newPath' => $newFile,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertFileDoesNotExist($file);
        $this->assertFileDoesNotExist($newFile);
    }
}
