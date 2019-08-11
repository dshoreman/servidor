<?php

namespace Servidor\FileManager;

use Symfony\Component\Finder\Finder;

class FileManager
{
    /**
     * @var Finder
     */
    private $finder;

    public function __construct()
    {
        $this->finder = new Finder;
    }

    public function list(string $path): array
    {
        $files = $this->finder->depth(0)->in($path);

        return array_map(function ($file) {
            return [
                'filename' => $file->getFilename(),
                'isDir' => $file->isDir(),
                'isFile' => $file->isFile(),
                'owner' => $file->getOwner(),
                'group' => $file->getGroup(),
                'perms' => $file->getPerms(),
            ];
        }, iterator_to_array($files, false));
    }
}
