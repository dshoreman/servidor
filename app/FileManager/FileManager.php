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
    private $file_perms;

    public function __construct()
    {
        $this->finder = new Finder;
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
                      ->sortByName($naturalSort = true)
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

        return $this->fileToArray($file, true);
    }

    private function loadPermissions($path)
    {
        $perms = [];

        exec("stat -c '%n %A %a' ${path}/*", $files);

        foreach ($files as $file) {
            list($filename, $text, $octal) = explode(' ', $file);

            $perms[$filename] = compact('text', 'octal');
        }

        $this->file_perms = $perms;
    }

    private function fileToArray($file, $includeContents = false): array
    {
        if (is_string($file)) {
            $path = explode('/', $file);
            $name = array_pop($path);

            $file = new SplFileInfo($file, implode('/', $path), $name);
        }

        $data = [
            'filename' => $file->getFilename(),
            'isDir' => $file->isDir(),
            'isFile' => $file->isFile(),
            'isLink' => $file->isLink(),
            'target' => $file->isLink() ? $file->getLinkTarget() : '',
            'owner' => posix_getpwuid($file->getOwner())['name'],
            'group' => posix_getgrgid($file->getGroup())['name'],
        ];

        $data['perms'] = is_null($this->file_perms) || !isset($this->file_perms[$data['filename']])
                       ? ['text' => '', 'octal' => mb_substr(decoct($file->getPerms()), -4)]
                       : $this->file_perms[$data['filename']];

        if (3 === mb_strlen($data['perms']['octal'])) {
            $data['perms']['octal'] = '0'.$data['perms']['octal'];
        }

        if ($includeContents) {
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
        }

        return $data;
    }
}
