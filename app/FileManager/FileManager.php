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
        $files = $this->finder->depth(0)->in($path)
                      ->ignoreDotFiles(false);

        return array_map(function ($file) {
            return [
                'filename' => $file->getFilename(),
                'isDir' => $file->isDir(),
                'isFile' => $file->isFile(),
                'isLink' => $file->isLink(),
                'target' => $file->isLink() ? $file->getLinkTarget() : '',
                'owner' => posix_getpwuid($file->getOwner())['name'],
                'group' => posix_getgrgid($file->getGroup())['name'],
                'perms' => mb_substr(decoct($file->getPerms()), -4),
            ];
        }, iterator_to_array($files, false));
    }
}
