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

    public function toggleArg(bool $cond, string $on, string $off = ''): self
    {
        $keyOn = array_search($on, $this->args);
        $keyOff = array_search($off, $this->args);

        if (is_int($keyOn)) {
            unset($this->args[$keyOn]);
        }

        if ('' != $keyOff && is_int($keyOff)) {
            unset($this->args[$keyOff]);
        }

        $arg = $cond ? $on : $off;

        if ('' != $arg) {
            $this->args[] = $arg;
        }

        return $this;
    }

    public function getOriginal(string $key)
    {
        return $this->original[$key] ?? null;
    }

    protected function setOriginal(): void
    {
        $this->original = $this->toArray();
    }
}
