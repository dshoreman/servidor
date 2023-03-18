<?php

namespace Servidor\System\Users;

use Servidor\System\LinuxCommand;
use Servidor\Traits\ToggleCommandArgs;

class LinuxUser extends LinuxCommand
{
    use ToggleCommandArgs;

    protected string $dir;

    protected ?int $gid;

    public ?int $uid;

    /**
     * @var array<string>
     */
    public array $groups = [];

    public string $name;

    public string $shell;

    /** @param array<string, mixed> $user */
    public function __construct(array $user = [], bool $loadGroups = false)
    {
        $this->gid = isset($user['gid']) ? (int) $user['gid'] : null;
        $this->uid = isset($user['uid']) ? (int) $user['uid'] : null;
        $this->dir = isset($user['dir']) ? (string) $user['dir'] : '';
        $this->name = (string) $user['name'];
        $this->shell = isset($user['shell']) ? (string) $user['shell'] : '';

        if ($loadGroups) {
            $this->loadGroups();
        }

        $this->setOriginal();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        if ($name !== (string) $this->getOriginal('name')) {
            $this->args[] = '-l ' . $name;
        }

        return $this;
    }

    public function setUid(?int $uid = null): self
    {
        if (isset($uid) && $uid > 0) {
            $this->uid = $uid;
        }

        if (isset($this->uid) && $this->getOriginal('uid') !== $this->uid) {
            $this->args[] = '-u ' . $this->uid;
        }

        return $this;
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

    /**
     * @param array<string>|null $groups
     */
    public function setGroups(?array $groups = null): self
    {
        if (\is_array($groups)) {
            $this->groups = $groups;
        }

        if (\is_array($groups) && $this->getOriginal('groups') !== $groups) {
            $this->args[] = '-G "' . implode(',', $this->groups) . '"';
        }

        return $this;
    }

    public function setShell(string $shell): self
    {
        if ('' !== $shell) {
            $this->shell = $shell;
        }

        if ($this->shell !== $this->getOriginal('shell')) {
            $this->args[] = '-s "' . $this->shell . '"';
        }

        return $this;
    }

    public function setSystem(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-r');
    }

    public function setCreateHome(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-m', '-M');
    }

    public function setHomeDirectory(string $dir): self
    {
        if ('' !== $dir && $dir !== $this->getOriginal('dir')) {
            $this->dir = $dir;
            $this->args[] = '-d "' . $this->dir . '"';
        }

        return $this;
    }

    public function setMoveHome(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-m');
    }

    public function setUserGroup(bool $enabled): self
    {
        return $this->toggleArg($enabled, '-U', '-N');
    }

    private function loadGroups(): void
    {
        $this->groups = [];
        $primary = explode(':', exec('getent group ' . (int) $this->gid));
        $effective = explode(' ', exec("groups {$this->name} | sed 's/.* : //'"));

        $primaryName = reset($primary);
        $primaryMembers = explode(',', end($primary));

        foreach ($effective as $group) {
            if ($group === $primaryName && !\in_array($group, $primaryMembers, true)) {
                continue;
            }

            $this->groups[] = $group;
        }
    }

    public function isDirty(): bool
    {
        return \count($this->args) > 0;
    }

    /** @return array{name: string, dir: string, groups: array<string>, shell: string, gid: ?int, uid: ?int} */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'dir' => $this->dir,
            'groups' => $this->groups,
            'shell' => $this->shell,
            'gid' => $this->gid,
            'uid' => $this->uid,
        ];
    }
}
