<?php

namespace Servidor\Projects\Services;

use Illuminate\Support\Str;
use Servidor\Projects\ProjectService;

class LogFile
{
    /** @var string */
    private $path;

    /** @var string */
    private $title;

    public function __construct(ProjectService $service, string $title, string $path)
    {
        $this->title = $title;

        $this->path = Str::startsWith($path, '/')
            ? $path : $service->source_root . '/' . $path;
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

        /** @var array<string> $file */
        return implode("\n", $file);
    }
}
