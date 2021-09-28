<?php

namespace Servidor\System;

abstract class LinuxCommand
{
    /**
     * @var array<string>
     */
    protected array $args = [];

    protected array $original = [];

    public function getArgs(): array
    {
        return $this->args;
    }

    public function hasArgs(): bool
    {
        return \count($this->args) > 0;
    }

    public function toArgs(): string
    {
        return implode(' ', $this->args);
    }

    /**
     * @return mixed
     */
    public function getOriginal(string $key)
    {
        return $this->original[$key] ?? null;
    }

    protected function setOriginal(): void
    {
        $this->original = $this->toArray();
    }

    abstract public function toArray(): array;
}
