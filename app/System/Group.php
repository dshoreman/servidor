<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\Exceptions\System\GroupNotFoundException;
use Servidor\Exceptions\System\GroupNotModifiedException;
use Servidor\Exceptions\System\GroupSaveException;
use Servidor\System\Groups\LinuxGroup;

class Group
{
    private const GROUP_NAME_TAKEN = 9;
    private const GROUP_GID_TAKEN = 4;
    private const GROUP_SYNTAX_INVALID = 2;
    private const GROUP_OPTION_INVALID = 3;

    /**
     * @var LinuxGroup
     */
    private $group;

    /**
     * @param array|LinuxGroup $group
     */
    public function __construct($group)
    {
        $this->group = $group instanceof LinuxGroup
                     ? $group : new LinuxGroup($group);
    }

    private function commit(string $cmd, ?string $args = null): int
    {
        $name = (string) $this->group->getOriginal('name');
        $args = $args ?: $this->group->toArgs();

        exec("sudo {$cmd} {$args} {$name}", $_, $retval);

        return $retval;
    }

    protected function commitAdd(): self
    {
        $retval = $this->commit('groupadd');

        if (0 === $retval) {
            return $this;
        }

        $error = 'Something unexpected happened! Exit code: ' . $retval;

        switch ($retval) {
            case self::GROUP_SYNTAX_INVALID:
                $error = 'Invalid command syntax.';
                break;
            case self::GROUP_OPTION_INVALID:
                $error = 'Invalid argument to option';
                break;
            case self::GROUP_GID_TAKEN:
                $error = "The group's GID must be unique";
                break;
            case self::GROUP_NAME_TAKEN:
                $error = 'The group name must be unique';
                break;
        }

        throw new GroupSaveException($error);
    }

    private function commitMod(): self
    {
        if ($this->group->hasArgs()) {
            $retval = $this->commit('groupmod');

            if (0 !== $retval) {
                throw new GroupSaveException("Couldn't update the group. Exit code: {$retval}.");
            }
        }

        if ($this->group->hasChangedUsers()) {
            $users = $this->group->users;

            $this->refresh($this->group->gid ?? $this->group->name)
                 ->group->setUsers($users);

            $retval = $this->commit('gpasswd', '-M "' . implode(',', $this->group->users) . '"');

            if (0 !== $retval) {
                throw new GroupSaveException("Couldn't update the group's users. Exit code: {$retval}.");
            }
        }

        return $this;
    }

    public static function create(string $name, bool $system = false, ?int $gid = null): array
    {
        $group = new self(
            (new LinuxGroup(['name' => $name]))
                ->setGid($gid ?: null)
                ->setSystem($system),
        );

        $group->commitAdd();

        return $group->refresh($name)->toArray();
    }

    public function delete(): void
    {
        exec('sudo groupdel ' . $this->group->name);
    }

    public static function find(int $gid): self
    {
        $group = posix_getgrgid($gid);

        if (!$group) {
            throw new GroupNotFoundException();
        }

        return new self($group);
    }

    public static function list(): Collection
    {
        exec('cat /etc/group', $lines);

        $keys = ['name', 'password', 'gid', 'users'];
        $groups = collect();

        foreach ($lines as $line) {
            assert(is_string($line));

            $group = array_combine($keys, explode(':', $line));
            $group['users'] = '' == $group['users'] ? [] : explode(',', $group['users']);

            $groups->push($group);
        }

        return $groups;
    }

    public function update(array $data): array
    {
        $this->group->setName((string) $data['name'])
                    ->setGid(isset($data['gid']) ? (int) $data['gid'] : null)
                    ->setUsers(isset($data['users']) ? (array) $data['users'] : null);

        if (!$this->group->isDirty()) {
            throw new GroupNotModifiedException();
        }

        $this->commitMod();

        return $this->refresh($this->group->gid ?? $this->group->name)->toArray();
    }

    /**
     * @param int|string $nameOrGid
     */
    private function refresh($nameOrGid): self
    {
        $arr = is_int($nameOrGid)
             ? posix_getgrgid($nameOrGid)
             : posix_getgrnam($nameOrGid);

        $this->group = new LinuxGroup($arr);

        return $this;
    }

    public function toArray(): array
    {
        return $this->group->toArray();
    }
}
