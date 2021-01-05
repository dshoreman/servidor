<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Str;
use Servidor\Projects\Application;

class LogFile
{
    private $path;

    private $title;

    public function __construct(Application $app, string $title, string $path)
    {
        $this->title = $title;

        $this->path = Str::startsWith($path, '/')
            ? $path : $app->source_root . '/' . $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function __toString(): string
    {
        exec('sudo cat ' . escapeshellarg($this->path), $file);

        return implode("\n", $file);
    }
}
