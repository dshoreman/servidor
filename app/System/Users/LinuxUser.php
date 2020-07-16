<?php

namespace Servidor\System\Users;

use Servidor\System\LinuxCommand;
use Servidor\Traits\ToggleCommandArgs;

class LinuxUser extends LinuxCommand
{
    use ToggleCommandArgs;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var ?int
     */
    protected $gid;

    /**
     * @var ?int
     */
    public $uid;

    /**
     * @var array
     */
    public $groups = [];

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $shell;

    public function __construct(array $user = [], bool $loadGroups = false)
    {
        $this->gid = $user['gid'] ? (int) $user['gid'] : null;
        $this->uid = $user['uid'] ? (int) $user['uid'] : null;
        $this->dir = (string) $user['dir'];
        $this->name = (string) $user['name'];
        $this->shell = (string) $user['shell'];

        if ($loadGroups) {
            $this->loadGroups();
        }

        $this->setOriginal();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        if ($name != $this->getOriginal('name')) {
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

    public function setGroups(?array $groups = null): self
    {
        if (is_array($groups)) {
            $this->groups = $groups;
        }

        if (is_array($groups) && $this->getOriginal('groups') !== $groups) {
            $this->args[] = '-G "' . implode(',', $this->groups) . '"';
        }

        return $this;
    }

    public function setShell(?string $shell): self
    {
        if (!is_null($shell)) {
            $this->shell = $shell;
        }

        if ($this->shell != $this->getOriginal('shell')) {
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
        if ('' != $dir && $dir != $this->getOriginal('dir')) {
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
            if ($group == $primaryName && !in_array($group, $primaryMembers)) {
                continue;
            }

            $this->groups[] = $group;
        }
    }

    public function isDirty(): bool
    {
        return count($this->args) > 0;
    }

    public function toArray(): array
    {
        $arr = [];

        foreach (['name', 'dir', 'groups', 'shell', 'gid', 'uid'] as $key) {
            if (isset($this->$key)) {
                $arr[$key] = $this->$key;
            }
        }

        return $arr;
    }
}
