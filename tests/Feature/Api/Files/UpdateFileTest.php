<?php

namespace Tests\Feature\Api\Files;

use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateFileTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files';

    /**
     * @test
     * @group broken-travis
     */
    public function authed_user_can_update_file(): void
    {
        $args = ['file' => resource_path('test-skel/updating.html')];

        file_put_contents($args['file'], 'empty');
        $file = $this->authed()->getJson($this->endpoint($args))->json();

        $response = $this->authed()->putJson($this->endpoint($args), [
            'contents' => 'File updated!',
        ]);

        $response->assertOk();
        $this->assertIsArray($response->json());

        $response->assertJsonStructure([
            'filename', 'filepath', 'mimetype', 'isDir', 'isFile',
            'isLink', 'target', 'owner', 'group', 'perms',
        ]);
        $response->assertJsonFragment([
            'contents' => 'File updated!',
            'filename' => 'updating.html',
            'filepath' => resource_path('test-skel'),
            'mimetype' => 'text/plain',
            'isFile' => true,
        ]);

        unlink($args['file']);
    }
}
