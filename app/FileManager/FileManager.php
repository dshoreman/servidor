<?php

namespace Servidor\FileManager;

use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileManager
{
    public const DECIMAL_PERMISSION_LENGTH = 3;

    private Finder $finder;

    /**
     * @var array<string, array{text: string, octal: string}>
     */
    private array $filePerms = [];

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function list(string $path): array
    {
        // Symfony's Finder trims all slashes from the end,
        // so we have to workaround it with this hack.
        if ('/' === $path) {
            $path = '/../';
        }

        $this->loadPermissions($path);

        try {
            return $this->getFiles($path);
        } catch (DirectoryNotFoundException $_) {
            throw new PathNotFound("This directory doesn't exist.");
        }
    }

    private function getFiles(string $path): array
    {
        /** @psalm-suppress TooManyArguments - sortByName */
        $files = $this->finder->depth(0)->in($path)
            ->sortByName(true)
            ->ignoreDotFiles(false);

        /** @var array{SplFileInfo|string} $files */
        $files = iterator_to_array($files, false);

        return array_map([$this, 'fileToArray'], $files);
    }

    public function createDir(string $path): array
    {
        if (file_exists($path)) {
            return ['error' => ['code' => 409, 'msg' => 'Path already exists']];
        }
        if (!mkdir($path) || !is_dir($path)) {
            return ['error' => ['code' => 500, 'msg' => 'Could not create ' . $path]];
        }

        return $this->open($path, false);
    }

    public function createFile(string $file, string $contents): array
    {
        if (file_exists($file)) {
            return ['error' => ['code' => 409, 'msg' => 'File already exists']];
        }
        if (!$this->save($file, $contents)) {
            return ['error' => ['code' => 500, 'msg' => 'Could not create ' . $file]];
        }

        return $this->open($file);
    }

    public function open(string $file, bool $includeContent = true): array
    {
        if (!file_exists($file)) {
            throw new PathNotFound('File not found');
        }

        $this->loadFilePermissions($file);

        if (false === $includeContent) {
            return $this->fileToArray($file);
        }

        return $this->fileWithContents($file);
    }

    public function save(string $file, string $contents): bool
    {
        return false !== file_put_contents($file, $contents);
    }

    public function move(string $path, string $target): array
    {
        if (!file_exists($path)) {
            return ['error' => ['code' => 404, 'msg' => 'File not found']];
        }
        if (file_exists($target)) {
            return ['error' => ['code' => 409, 'msg' => 'Target already exists']];
        }

        try {
            rename($path, $target);

            return $this->open($target);
        } catch (UnsupportedFileType $_) {
            return $this->open($target, false);
        }
    }

    public function delete(string $path): bool
    {
        if (!file_exists($path)) {
            return true;
        }
        if (!is_writable($path)) {
            throw new PathNotWritable('No permission to write path');
        }
        if (is_dir($path)) {
            return rmdir($path);
        }

        return unlink($path);
    }

    private function loadFilePermissions(string $path): array
    {
        $pathParts = explode('/', $path);

        $name = array_pop($pathParts);
        $match = mb_strrpos($path, '/');
        if (false === $match) {
            throw new InvalidArgumentException();
        }
        $path = (string) mb_substr($path, 0, $match);

        return $this->loadPermissions($path, $name);
    }

    private function loadPermissions(string $path, string $name = '.* *'): array
    {
        $perms = [];

        exec('cd "' . $path . '" && stat -c "%n %A %a" ' . $name . ' 2>/dev/null', $files);

        foreach ($files as $file) {
            assert(is_string($file));

            [$filename, $text, $octal] = explode(' ', $file);

            $perms[$filename] = compact('text', 'octal');
        }

        return $this->filePerms = $perms;
    }

    /**
     * TODO: See if the ErrorControlOperator still needs
     * to be suppressed once phpmd is working on PHP 8.x.
     *
     * @param SplFileInfo|string $file
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     *
     * @return array{0: SplFileInfo, 1: array}
     */
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
            'mimetype' => @mime_content_type((string) $file->getRealPath()),
            'isDir' => $file->isDir(),
            'isFile' => $file->isFile(),
            'isLink' => $file->isLink(),
            'target' => $file->isLink() ? $file->getLinkTarget() : '',
            'owner' => $owner['name'] ?? '???',
            'group' => $group['name'] ?? '???',
        ];

        $data['perms'] = $this->filePerms[$data['filename']];

        if (self::DECIMAL_PERMISSION_LENGTH === mb_strlen($data['perms']['octal'])) {
            $data['perms']['octal'] = '0' . $data['perms']['octal'];
        }

        return [$file, $data];
    }

    /** @param SplFileInfo|string $file */
    private function fileToArray($file): array
    {
        [$_, $data] = $this->loadFile($file);

        return $data;
    }

    private function fileWithContents(string $file): array
    {
        [$file, $data] = $this->loadFile($file);

        if ($data['mimetype'] && 'text/' !== mb_substr((string) $data['mimetype'], 0, 5)) {
            throw new UnsupportedFileType('Unsupported filetype');
        }

        try {
            $data['contents'] = $file->getContents();
        } catch (RuntimeException $e) {
            $msg = $e->getMessage();
            $data['contents'] = '';
            $data['error'] = ['code' => 418, 'msg' => $msg];
            $deniedError = 'failed to open stream: permission denied';

            if (Str::contains((string) mb_strtolower($msg), $deniedError)) {
                $data['error'] = ['code' => 403, 'msg' => 'Permission denied'];
            }
        }

        return $data;
    }
}
