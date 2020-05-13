<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
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

    /** @test */
    public function nonexistant_paths_return_error_with_filepath(): void
    {
        $response = $this->authed()->getJson($this->endpoint([
            'path' => '/tmp/nothere',
        ]), [
            'contents' => 'invalid file',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'filepath' => '/tmp/nothere',
            'error' => [
                'code' => 404,
                'msg' => "This directory doesn't exist.",
            ],
        ]);
    }
}
