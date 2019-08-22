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
    public function list_correctly_identifies_symlinks()
    {
        $list = $this->manager->list($this->dummy('mixed/another-dir'));

        $this->assertTrue($list[0]['isLink']);
        $this->assertTrue($list[0]['isFile']);
        $this->assertFalse($list[0]['isDir']);
        $this->assertArrayHasKey('target', $list[0]);
        $this->assertEquals('baz-link', $list[0]['filename']);
        $this->assertEquals('../../hidden/.baz.txt', $list[0]['target']);
    }

    /** @test */
    public function list_includes_hidden_files()
    {
        $list = $this->manager->list($this->dummy('hidden'));

        $this->assertIsArray($list);
        $this->assertCount(3, $list);
        $this->assertTrue($list[0]['isFile']);
        $this->assertEquals('.bar', $list[0]['filename']);
    }

    /** @test */
    public function list_can_show_files_in_system_root()
    {
        $list = $this->manager->list('/');

        $this->assertIsArray($list);

        $matches = array_filter($list, function ($a) {
            return in_array($a['filename'], ['bin', 'etc', 'home', 'usr', 'var']);
        });

        $expected = [
            'isDir' => true,
            'isFile' => false,
            'owner' => 'root',
            'group' => 'root',
            'perms' => '0755',
        ];

        $this->assertCount(5, $matches);
        foreach ($matches as $match) {
            $this->assertArraySubset($expected, $match);
        }
    }

    /** @test */
    public function show_returns_contents_of_given_file()
    {
        $file = $this->manager->open($this->dummy('mixed/hello.md'));

        $this->assertIsArray($file);
        $this->assertArrayHasKey('contents', $file);
        $this->assertEquals(
            "# Hello World\n\n> What would you like to do today?\n",
            $file['contents'],
        );
    }

    /** @test */
    public function show_catches_runtime_exceptions()
    {
        $file = $this->manager->open($this->dummy('protected/forbidden'));

        $this->assertIsArray($file);
        $this->assertEmpty($file['contents']);
        $this->assertArrayHasKey('error', $file);
        $this->assertIsArray($file['error']);
        $this->assertSame([
            'code' => 403,
            'msg' => 'Permission denied',
        ], $file['error']);
    }

    /** @test */
    public function show_includes_details_about_the_file()
    {
        $file = $this->manager->open($this->dummy('mixed/hello.md'));
        unset($file['contents']);

        $this->assertSame([
            'filename' => 'hello.md',
            'isDir' => false,
            'isFile' => true,
            'isLink' => false,
            'target' => '',
            'owner' => 'www-data',
            'group' => 'www-data',
            'perms' => '0664',
        ], $file);
    }

    /** @test */
    public function show_follows_symlinks()
    {
        $file = $this->manager->open($this->dummy('mixed/another-dir/baz-link'));

        $this->assertEquals("link me!\n", $file['contents']);
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
