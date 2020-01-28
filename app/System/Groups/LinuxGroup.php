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
    public $gid;

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $users = '';

    /**
     * @var array
     */
    private $original;

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

    public function setName(string $name): self
    {
        $this->name = $name;

        if ($name != $this->getOriginal('name')) {
            $this->args[] = '-n ' . $name;
        }

        return $this;
    }

    public function setUsers(?array $users): self
    {
        if (is_array($users)) {
            $this->users = $users;
        }

        return $this;
    }

    public function hasArgs(): bool
    {
        return count($this->args) > 0;
    }

    public function hasChangedUsers(): bool
    {
        return $this->users != $this->getOriginal('users');
    }

    public function isDirty()
    {
        return $this->hasArgs() || $this->hasChangedUsers();
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
