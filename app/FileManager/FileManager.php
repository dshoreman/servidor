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

    /**
     * @return array<int, array<string, mixed>>
     */
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getFiles(string $path): array
    {
        /** @psalm-suppress TooManyArguments - sortByName */
        $files = $this->finder->depth(0)->in($path)
            ->sortByName(true)
            ->ignoreDotFiles(false)
        ;

        /** @var array{SplFileInfo|string} $files */
        $files = iterator_to_array($files, false);

        return array_map([$this, 'fileToArray'], $files);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
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

    /** @return array<string,array{text:string,octal:string}> */
    private function loadFilePermissions(string $path): array
    {
        $pathParts = explode('/', $path);

        $name = array_pop($pathParts);
        $match = mb_strrpos($path, '/');
        if (false === $match) {
            throw new InvalidArgumentException();
        }
        $path = mb_substr($path, 0, $match);

        return $this->loadPermissions($path, $name);
    }

    /** @return array<string, array{text: string, octal: string}> */
    private function loadPermissions(string $path, string $name = '.* *'): array
    {
        $perms = [];

        exec('cd "' . $path . '" && stat -c "%n %A %a" ' . $name . ' 2>/dev/null', $files);

        foreach ($files as $file) {
            \assert(\is_string($file));

            [$filename, $text, $octal] = explode(' ', $file);

            if (self::DECIMAL_PERMISSION_LENGTH === mb_strlen($octal)) {
                $octal = '0' . $octal;
            }

            $perms[$filename] = compact('text', 'octal');
        }

        return $this->filePerms = $perms;
    }

    /**
     * @return array{0: SplFileInfo, 1: array<string, mixed>}
     */
    private function loadFile(SplFileInfo|string $file): array
    {
        if (\is_string($file)) {
            $path = explode('/', $file);
            $name = array_pop($path);

            $file = new SplFileInfo($file, implode('/', $path), $name);
        }

        return [$file, $this->makeFileData($file)];
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     *
     * @return array<string,mixed>
     */
    private function makeFileData(SplFileInfo $file): array
    {
        $filename = $file->getFilename();

        return [
            'filename' => $filename,
            'filepath' => $file->getPath(),
            'mimetype' => @mime_content_type((string) $file->getRealPath()),
            'isDir' => $file->isDir(),
            'isFile' => $file->isFile(),
            'isLink' => $file->isLink(),
            'target' => $file->isLink() ? $file->getLinkTarget() : '',
            'owner' => (posix_getpwuid($file->getOwner()) ?: [])['name'] ?? '???',
            'group' => (posix_getgrgid($file->getGroup()) ?: [])['name'] ?? '???',
            'perms' => $this->filePerms[$filename],
        ];
    }

    /**
     * @param SplFileInfo|string $file
     *
     * @return array<string, mixed>
     */
    private function fileToArray($file): array
    {
        [$_, $data] = $this->loadFile($file);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function fileWithContents(string $file): array
    {
        [$file, $data] = $this->loadFile($file);

        if (
            $data['mimetype']
            && 'application/x-empty' !== $data['mimetype']
            && 'text/' !== mb_substr((string) $data['mimetype'], 0, 5)
        ) {
            throw new UnsupportedFileType("Unsupported filetype {$data['mimetype']}");
        }

        try {
            $data['contents'] = $file->getContents();
        } catch (RuntimeException $e) {
            $data = $this->addErrorFromException($e, $data);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function addErrorFromException(RuntimeException $e, array $data): array
    {
        $msg = $e->getMessage();

        $data = array_merge($data, [
            'error' => ['code' => 418, 'msg' => $msg],
            'contents' => '',
        ]);

        if (Str::contains(mb_strtolower($msg), 'failed to open stream: permission denied')) {
            $data['error'] = ['code' => 403, 'msg' => 'Permission denied'];
        }

        return $data;
    }
}
