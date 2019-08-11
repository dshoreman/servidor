<?php

namespace Servidor\FileManager;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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

        return array_map(
            [$this, 'fileToArray'],
            iterator_to_array($files, false),
        );
    }

    public function open($file): array
    {
        return $this->fileToArray($file, true);
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
            'perms' => mb_substr(decoct($file->getPerms()), -4),
        ];

        if ($includeContents) {
            $data['contents'] = $file->getContents();
        }

        return $data;
    }
}
