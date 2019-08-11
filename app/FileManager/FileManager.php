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

        return iterator_to_array($files);
    }
}
