<?php

namespace Tests\Feature\Api\Files;

use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class ShowFileTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/files';

    /** @test */
    public function authed_user_can_view_file_contents(): void
    {
        $file = resource_path('test-skel/mixed/hello.md');
        $response = $this->authed()->getJson($this->endpoint(['file' => $file]));

        $response->assertOk();
        $this->assertIsArray($response->json());

        $response->assertJsonStructure(['filename', 'filepath', 'contents']);
        $response->assertJson(['contents' => file_get_contents($file)]);
    }

    /** @test */
    public function nonexistant_files_return_not_found_error(): void
    {
        $response = $this->authed()->getJson($this->endpoint(['file' => '/tmp/nothere.txt']));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['error' => ['code', 'msg']]);
        $response->assertJsonFragment(['code' => 404, 'msg' => 'File not found']);
    }

    /** @test */
    public function non_text_files_show_unsupported_filetype_error(): void
    {
        $response = $this->authed()->getJson($this->endpoint([
            'file' => resource_path('test-skel/files/image.png'),
        ]));

        $response->assertStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        $response->assertJsonStructure(['filename', 'filepath', 'error' => ['code', 'msg']]);
        $response->assertJsonFragment(['code' => 415, 'msg' => 'Unsupported filetype image/png']);
        // Maybe shouldn't have this assertion? Not sure if mime is used on frontend
        // $response->assertJsonFragment(['isFile' => true, 'mimetype' => 'image/png']);
    }
}
