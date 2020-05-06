<?php

namespace Tests\Feature\Api\Files;

use Tests\RequiresAuth;
use Tests\TestCase;

class ListFilesTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files';

    /** @test */
    public function authed_user_can_list_web_root(): void
    {
        $response = $this->authed()->getJson($this->endpoint([
            'path' => resource_path('test-skel/mixed'),
        ]));

        $response->assertOk();
        $this->assertIsArray($response->json());

        $response->assertJsonStructure([[
            'filename', 'filepath', 'mimetype', 'isDir', 'isFile',
            'isLink', 'target', 'owner', 'group', 'perms',
        ]]);
        $response->assertJsonFragment([
            'filename' => 'hello.md',
            'filepath' => resource_path('test-skel/mixed'),
            'mimetype' => 'text/plain',
        ]);
    }
}
