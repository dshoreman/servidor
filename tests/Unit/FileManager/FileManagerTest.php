<?php

namespace Tests\Unit\FileManager;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPUnit\Framework\TestCase;
use Servidor\FileManager\FileManager;
use Servidor\FileManager\PathNotFound;

class FileManagerTest extends TestCase
{
    use ArraySubsetAsserts;

    /**
     * @var FileManager
     */
    private $manager;

    public function setUp(): void
    {
        $this->manager = new FileManager();
    }

    /** @test */
    public function list_returns_items_in_given_path(): void
    {
        $list = $this->manager->list($this->dummy('mixed'));

        $this->assertIsArray($list);
        $this->assertCount(3, $list);

        $this->assertSame([
            'filename' => 'another-dir',
            'filepath' => $this->dummy('mixed'),
            'mimetype' => 'directory',
            'isDir' => true,
            'isFile' => false,
            'isLink' => false,
            'target' => '',
            'owner' => 'www-data',
            'group' => 'www-data',
            'perms' => [
                'text' => 'drwxrwxr-x',
                'octal' => '0775',
            ],
        ], $list[0]);
    }

    /** @test */
    public function list_correctly_identifies_symlinks(): void
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
    public function list_includes_hidden_files(): void
    {
        $list = $this->manager->list($this->dummy('hidden'));

        $this->assertIsArray($list);
        $this->assertCount(3, $list);
        $this->assertTrue($list[0]['isFile']);
        $this->assertEquals('.bar', $list[0]['filename']);
    }

    /** @test */
    public function permissions_are_loaded_for_hidden_files(): void
    {
        $list = $this->manager->list($this->dummy('hidden'));

        $this->assertCount(3, $list);
        $this->assertEquals([
            'text' => '-rw-rw-r--',
            'octal' => '0664',
        ], $list[0]['perms']);
    }

    /** @test */
    public function list_can_show_files_in_system_root(): void
    {
        $dirs = ['bin', 'etc', 'home', 'usr', 'var'];
        $expected = [
            'isDir' => true,
            'isFile' => false,
            'owner' => 'root',
            'group' => 'root',
        ];
        $list = $this->manager->list('/');

        $this->assertIsArray($list);

        $matches = array_filter($list, static fn ($a) => \in_array($a['filename'], $dirs, true));

        $this->assertCount(5, $matches);
        foreach ($matches as $match) {
            $this->assertArraySubset($expected, $match);
        }
    }

    /** @test */
    public function open_returns_contents_of_given_file(): void
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
    public function open_catches_permission_denied_errors(): void
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
    public function open_catches_stat_failed_error_when_file_does_not_exist(): void
    {
        $this->expectException(PathNotFound::class);

        $this->manager->open($this->dummy('invalid/file'));
    }

    /** @test */
    public function open_includes_details_about_the_file(): void
    {
        $file = $this->manager->open($this->dummy('mixed/hello.md'));
        unset($file['contents']);

        $this->assertSame([
            'filename' => 'hello.md',
            'filepath' => $this->dummy('mixed'),
            'mimetype' => 'text/plain',
            'isDir' => false,
            'isFile' => true,
            'isLink' => false,
            'target' => '',
            'owner' => 'www-data',
            'group' => 'www-data',
            'perms' => [
                'text' => '-rw-rw-r--',
                'octal' => '0664',
            ],
        ], $file);
    }

    /** @test */
    public function open_follows_symlinks(): void
    {
        $file = $this->manager->open($this->dummy('mixed/another-dir/baz-link'));

        $this->assertEquals("link me!\n", $file['contents']);
    }

    /**
     * Get the path to a file within the test skeleton.
     */
    private function dummy(string $path): string
    {
        return resource_path('test-skel/' . $path);
    }
}
