<?php

namespace Servidor\FileManager;

use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileManager
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var array
     */
    private $filePerms;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function list(string $path): array
    {
        // Symfony's Finder trims all slashes from the end,
        // so we have to workaround it with this hack.
        if ('/' == $path) {
            $path = '/../';
        }

        $this->loadPermissions($path);

        try {
            return $this->getFiles($path);
        } catch (DirectoryNotFoundException $e) {
            return [
                'filepath' => $path,
                'error' => [
                    'code' => 404,
                    'msg' => "This directory doesn't exist.",
                ],
            ];
        }
    }

    private function getFiles(string $path): array
    {
        $files = $this->finder->depth(0)->in($path)
                      ->sortByName(true)
                      ->ignoreDotFiles(false);

        return array_map(
            [$this, 'fileToArray'],
            iterator_to_array($files, false),
        );
    }

    public function createDir($path): array
    {
        if (file_exists($path)) {
            return ['error' => ['code' => 409, 'msg' => 'Path already exists']];
        }
        if (!mkdir($path) || !is_dir($path)) {
            return ['error' => ['code' => 500, 'msg' => 'Could not create ' . $path]];
        }

        $dir = $this->open($path);
        if ('Unsupported filetype' === $dir['error']['msg'] ?? '') {
            unset($dir['error']);
        }

        return $dir;
    }

    public function createFile($file, $contents): array
    {
        if (file_exists($file)) {
            return ['error' => ['code' => 409, 'msg' => 'File already exists']];
        }
        if (!$this->save($file, $contents)) {
            return ['error' => ['code' => 500, 'msg' => 'Could not create ' . $file]];
        }

        return $this->open($file);
    }

    public function open($file): array
    {
        if (!file_exists($file)) {
            return ['error' => ['code' => 404, 'msg' => 'File not found']];
        }

        $this->loadFilePermissions($file);

        return $this->fileWithContents($file);
    }

    public function save($file, $contents): bool
    {
        return false !== file_put_contents($file, $contents);
    }

    public function move($path, $target): array
    {
        if (!file_exists($path)) {
            return ['error' => ['code' => 404, 'msg' => 'File not found']];
        }
        if (file_exists($target)) {
            return ['error' => ['code' => 409, 'msg' => 'Target already exists']];
        }
        if (!rename($path, $target)) {
            return ['error' => ['code' => 500, 'msg' => 'Rename operation failed']];
        }

        return $this->open($target);
    }

    public function delete($path)
    {
        $remove = is_dir($path) ? 'rmdir' : 'unlink';
        $error = ['code' => 500, 'msg' => 'Failed removing' . $path];

        if (!file_exists($path)) {
            return ['error' => null];
        }
        if (!is_writable($path)) {
            return ['error' => ['code' => 403, 'msg' => 'No permission to write path']];
        }

        return ['error' => $remove($path) ? null : $error];
    }

    private function loadFilePermissions(string $path): array
    {
        $pathParts = explode('/', $path);

        $name = array_pop($pathParts);
        $path = mb_substr($path, 0, mb_strrpos($path, '/'));

        return $this->loadPermissions($path, $name);
    }

    private function loadPermissions(string $path, string $name = '.* *'): array
    {
        $perms = [];

        exec('cd "' . $path . '" && stat -c "%n %A %a" ' . $name . ' 2>/dev/null', $files);

        foreach ($files as $file) {
            list($filename, $text, $octal) = explode(' ', $file);

            $perms[$filename] = compact('text', 'octal');
        }

        return $this->filePerms = $perms;
    }

    private function loadFile($file): array
    {
        if (is_string($file)) {
            $path = explode('/', $file);
            $name = array_pop($path);

            $file = new SplFileInfo($file, implode('/', $path), $name);
        }

        $owner = posix_getpwuid($file->getOwner()) ?: [];
        $group = posix_getgrgid($file->getGroup()) ?: [];

        $data = [
            'filename' => $file->getFilename(),
            'filepath' => $file->getPath(),
            'mimetype' => @mime_content_type($file->getRealPath()),
            'isDir' => $file->isDir(),
            'isFile' => $file->isFile(),
            'isLink' => $file->isLink(),
            'target' => $file->isLink() ? $file->getLinkTarget() : '',
            'owner' => $owner['name'] ?? '???',
            'group' => $group['name'] ?? '???',
        ];

        $data['perms'] = $this->filePerms[$data['filename']];

        if (intval(3) === mb_strlen($data['perms']['octal'])) {
            $data['perms']['octal'] = '0' . $data['perms']['octal'];
        }

        return [$file, $data];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fileToArray($file): array
    {
        list($file, $data) = $this->loadFile($file);

        return $data;
    }

    private function fileWithContents($file): array
    {
        list($file, $data) = $this->loadFile($file);

        if ($data['mimetype'] && 'text/' != mb_substr($data['mimetype'], 0, 5)) {
            return array_merge($data, ['error' => [
                'code' => 415,
                'msg' => 'Unsupported filetype',
            ]]);
        }

        try {
            $data['contents'] = $file->getContents();
        } catch (RuntimeException $e) {
            $msg = $e->getMessage();
            $data['contents'] = '';
            $data['error'] = ['code' => 418, 'msg' => $msg];

            if (Str::contains($msg, 'failed to open stream: Permission denied')) {
                $data['error'] = ['code' => 403, 'msg' => 'Permission denied'];
            }
        }

        return $data;
    }
}
