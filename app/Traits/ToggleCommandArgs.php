<?php

namespace Servidor\Traits;

trait ToggleCommandArgs
{
    /**
     * @var array<string>
     */
    protected array $args = [];

    /** @return static */
    public function toggleArg(bool $cond, string $on, string $off = ''): self
    {
        $keyOn = array_search($on, $this->args, true);
        $keyOff = array_search($off, $this->args, true);

        if (\is_int($keyOn)) {
            unset($this->args[$keyOn]);
        }

        if ('' !== $keyOff && \is_int($keyOff)) {
            unset($this->args[$keyOff]);
        }

        $arg = $cond ? $on : $off;

        if ('' !== $arg) {
            $this->args[] = $arg;
        }

        return $this;
    }
}
