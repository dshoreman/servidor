<?php

namespace Servidor\System\Users;

class LinuxUser
{
    /**
     * @var array
     */
    private $args = [];

    /**
     * @var int
     */
    protected $gid;

    /**
     * @var int
     */
    public $uid;

    /**
     * @var array
     */
    protected $original = [];

    /**
     * @var array
     */
    public $groups;

    /**
     * @var string
     */
    public $name;

    public function __construct(array $user = [], bool $loadGroups = false)
    {
        $this->name = $user['name'] ?? '';

        if (isset($user['uid'])) {
            $this->uid = $user['uid'];
        }

        if (isset($user['gid'])) {
            $this->gid = $user['gid'];
        }

        if ($loadGroups) {
            $this->loadGroups();
        }

        $this->original = $this->toArray();
    }

    public function getOriginal(string $key)
    {
        return $this->original[$key] ?? null;
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

    public function setCreateHome(bool $enabled): self
    {
        $keyOn = array_search('-m', $this->args);
        $keyOff = array_search('-M', $this->args);

        if (is_int($keyOn)) {
            unset($this->args[$keyOn]);
        }
        if (is_int($keyOff)) {
            unset($this->args[$keyOff]);
        }

        $this->args[] = $enabled ? '-m' : '-M';

        return $this;
    }

    private function loadGroups(): void
    {
        $this->groups = [];
        $primary = explode(':', exec('getent group ' . $this->gid));
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

    public function toArgs(): string
    {
        return implode(' ', $this->args);
    }

    public function toArray(): array
    {
        $arr = [];

        foreach (['name', 'groups', 'gid', 'uid'] as $key) {
            if (isset($this->$key)) {
                $arr[$key] = $this->$key;
            }
        }

        return $arr;
    }
}
