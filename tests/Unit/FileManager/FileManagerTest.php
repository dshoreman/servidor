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
        $list = $this->manager->list('/etc');

        $this->assertIsArray($list);
        $this->assertNotCount(0, $list);

        $item = $list[0];

        $this->assertArrayHasKey('filename', $item);
        $this->assertArrayHasKey('isDir', $item);
        $this->assertArrayHasKey('isFile', $item);
        $this->assertArrayHasKey('isLink', $item);
        $this->assertArrayHasKey('target', $item);
        $this->assertArrayHasKey('owner', $item);
        $this->assertArrayHasKey('group', $item);
        $this->assertArrayHasKey('perms', $item);
    }

    /** @test */
    public function list_includes_hidden_files()
    {
        $list = $this->manager->list('/etc/skel');

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
        $file = $this->manager->open('/etc/hostname');

        $this->assertIsArray($file);
        $this->assertArrayHasKey('contents', $file);
        $this->assertNotEmpty($file['contents']);
    }
}
