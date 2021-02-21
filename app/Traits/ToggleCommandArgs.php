<?php

namespace Servidor\Traits;

trait ToggleCommandArgs
{
    protected array $args = [];

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
}
