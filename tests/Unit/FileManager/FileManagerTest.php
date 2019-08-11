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
    public function list()
    {
        $list = $this->manager->list('/var/servidor');

        $this->assertIsArray($list);
        $this->assertNotCount(0, $list);

        $item = $list[0];

        $this->assertArrayHasKey('filename', $list[0]);
        $this->assertArrayHasKey('isDir', $list[0]);
        $this->assertArrayHasKey('isFile', $list[0]);
        $this->assertArrayHasKey('isLink', $list[0]);
        $this->assertArrayHasKey('target', $list[0]);
        $this->assertArrayHasKey('owner', $list[0]);
        $this->assertArrayHasKey('group', $list[0]);
        $this->assertArrayHasKey('perms', $list[0]);
    }

    /** @test */
    public function list_includes_hidden_files()
    {
        $list = $this->manager->list('/var/servidor');

        $hidden = array_filter($list, function ($file) {
            return '.' === mb_substr($file['filename'], 0, 1);
        });

        $this->assertNotEmpty($hidden);
    }
}
