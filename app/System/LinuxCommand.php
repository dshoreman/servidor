<?php

namespace Servidor\System;

abstract class LinuxCommand
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $original = [];

    public function getArgs(): array
    {
        return $this->args;
    }

    public function hasArgs(): bool
    {
        return count($this->args) > 0;
    }

    protected function initArgs(array $args, array $data): void
    {
        foreach ($args as $alias => $key) {
            if (!isset($data[$key])) {
                continue;
            }

            if (is_int($alias)) {
                $alias = $key;
            }

            $this->$alias = $data[$key];
        }
    }

    public function toArgs(): string
    {
        return implode(' ', $this->args);
    }

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
