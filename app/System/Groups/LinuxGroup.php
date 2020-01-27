<?php

namespace Servidor\System\Groups;

class LinuxGroup
{
    /**
     * @var array
     */
    private $args = [];

    /**
     * @var int
     */
    private $gid;

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    private $users = '';

    public function __construct(array $group = [])
    {
        $this->name = $group['name'] ?? '';

        if (isset($group['gid'])) {
            $this->gid = $group['gid'];
        }

        if (isset($group['members'])) {
            $this->users = $group['members'];
        }

        $this->original = $this->toArray();
    }

    public function getOriginal(string $key)
    {
        return $this->original[$key] ?? null;
    }

    public function setGid(?int $gid = null): self
    {
        if (isset($gid) && $gid > 0) {
            $this->gid = $gid;
        }

        if (isset($this->gid) && $this->getOriginal('gid') !== $this->gid) {
            $this->args[] = '-g ' . $this->gid;
        }

        return $this;
    }

    public function toArgs(): string
    {
        return implode(' ', $this->args);
    }

    public function toArray(): array
    {
        return [
            'gid' => $this->gid ?? null,
            'name' => $this->name ?? '',
            'users' => $this->users ?? '',
        ];
    }
}
