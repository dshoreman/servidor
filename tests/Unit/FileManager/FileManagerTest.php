<?php

namespace Tests\Unit\FileManager;

use Servidor\FileManager\FileManager;
use Tests\TestCase;

class FileManagerTest extends TestCase
{
    /**
     * @var FileManager
     */
    private $manager;

    public function setUp()
    {
        $this->manager = new FileManager;
    }

    /** @test */
    public function list_returns_items_in_given_path()
    {
        $list = $this->manager->list($this->dummy('mixed'));

        $this->assertIsArray($list);
        $this->assertCount(3, $list);

        $this->assertSame([
            'filename' => 'another-dir',
            'isDir' => true,
            'isFile' => false,
            'isLink' => false,
            'target' => '',
            'owner' => 'www-data',
            'group' => 'www-data',
            'perms' => '0775',
        ], $list[0]);
    }

    /** @test */
    public function list_includes_hidden_files()
    {
        $list = $this->manager->list($this->dummy('hidden'));

        $hidden = array_filter($list, function ($file) {
            return '.' === mb_substr($file['filename'], 0, 1);
        });

        $this->assertNotEmpty($hidden);
    }

    /** @test */
    public function list_can_show_files_in_system_root()
    {
        $list = $this->manager->list('/');

        $this->assertIsArray($list);
        $this->assertNotCount(0, $list);
    }

    /** @test */
    public function show_returns_contents_of_given_file()
    {
        $file = $this->manager->open($this->dummy('mixed/hello.md'));

        $this->assertIsArray($file);
        $this->assertArrayHasKey('contents', $file);
        $this->assertNotEmpty($file['contents']);
    }

    /**
     * Get the path to a file within the test skeleton.
     *
     * @param string $path
     *
     * @return string
     */
    private function dummy(string $path): string
    {
        return resource_path('test-skel/'.$path);
    }
}
