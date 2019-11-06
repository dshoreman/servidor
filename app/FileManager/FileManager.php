<?php

namespace Servidor\FileManager;

use Illuminate\Http\Response;
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

        $files = $this->finder->depth(0)->in($path)
                      ->sortByName(true)
                      ->ignoreDotFiles(false);

        return array_map(
            [$this, 'fileToArray'],
            iterator_to_array($files, false),
        );
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

        $data = [
            'filename' => $file->getFilename(),
            'mimetype' => @mime_content_type($file->getRealPath()),
            'isDir' => $file->isDir(),
            'isFile' => $file->isFile(),
            'isLink' => $file->isLink(),
            'target' => $file->isLink() ? $file->getLinkTarget() : '',
            'owner' => posix_getpwuid($file->getOwner())['name'],
            'group' => posix_getgrgid($file->getGroup())['name'],
        ];

        $data['perms'] = is_null($this->filePerms) || !isset($this->filePerms[$data['filename']])
                       ? ['text' => '', 'octal' => mb_substr(decoct($file->getPerms()), -4)]
                       : $this->filePerms[$data['filename']];

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
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage();
            $data['contents'] = '';

            $data['error'] = str_contains($msg, 'Permission denied') ? [
                'code' => Response::HTTP_FORBIDDEN,
                'msg' => 'Permission denied',
            ] : ['code' => 418, 'msg' => $msg];
        }

        return $data;
    }
}
