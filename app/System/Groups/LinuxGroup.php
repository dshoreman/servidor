<?php

namespace Servidor\System\Groups;

use Servidor\System\LinuxCommand;
use Servidor\Traits\ToggleCommandArgs;

class LinuxGroup extends LinuxCommand
{
    use ToggleCommandArgs;

    public ?int $gid;

    public string $name;

    public array $users;

    public function __construct(array $group = [])
    {
        $this->gid = ($group['gid'] ?? null) ? (int) $group['gid'] : null;
        $this->name = (string) $group['name'];
        $this->users = (array) ($group['members'] ?? []);

        $this->setOriginal();
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

    public function setSystem(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-r');
    }

    public function setUsers(?array $users): self
    {
        if (is_array($users)) {
            $this->users = $users;
        }

        return $this;
    }

    public function hasChangedUsers(): bool
    {
        return $this->users != $this->getOriginal('users');
    }

    public function isDirty(): bool
    {
        return $this->hasArgs() || $this->hasChangedUsers();
    }

    public function toArray(): array
    {
        return [
            'gid' => $this->gid ?? null,
            'name' => $this->name ?: '',
            'users' => $this->users,
        ];
    }
}
