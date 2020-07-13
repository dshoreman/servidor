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
        $this->assertFileNotExists($newFile);
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
        $this->assertFileNotExists($file);
        $this->assertFileExists($newFile);
        $this->assertStringEqualsFile($newFile, 'temp');

        unlink($newFile);
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
}
