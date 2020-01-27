<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\Exceptions\System\GroupSaveException;
use Servidor\System\Groups\LinuxGroup;

class Group
{
    private const GROUP_NAME_TAKEN = 9;
    private const GROUP_GID_TAKEN = 4;
    private const GROUP_SYNTAX_INVALID = 2;
    private const GROUP_OPTION_INVALID = 3;
    private const GROUP_UPDATE_FAILED = 10;

    /**
     * @var LinuxGroup
     */
    private $group;

    public function __construct($group)
    {
        $this->group = $group instanceof LinuxGroup
                     ? $group : new LinuxGroup($group);
    }

    private function commit(string $cmd): self
    {
        $name = $this->group->getOriginal('name');

        exec("sudo {$cmd} {$this->group->toArgs()} {$name}", $output, $retval);
        unset($output);

        if (0 === $retval) {
            return $this;
        }

        switch ($retval) {
            case self::GROUP_SYNTAX_INVALID:
                $error = 'Invalid command syntax.';
                break;
            case self::GROUP_OPTION_INVALID:
                $error = 'Invalid argument to option';
                break;
            case self::GROUP_GID_TAKEN:
                $error = 'GID not unique (when -o not used)';
                break;
            case self::GROUP_NAME_TAKEN:
                $error = 'Group name not unique';
                break;
            case self::GROUP_UPDATE_FAILED:
                $error = "Can't update group file";
                break;
        }

        throw new GroupSaveException($error ?: 'Something unexpected happened!');
    }

    public static function create(string $name, ?int $gid = null): array
    {
        $group = new self(
            (new LinuxGroup(['name' => $name]))
                ->setGid($gid ?: null),
        );

        $group->commit('groupadd');

        return $group->refresh($name)->toArray();
    }

    public static function list(): Collection
    {
        exec('cat /etc/group', $lines);

        $keys = ['name', 'password', 'gid', 'users'];
        $groups = collect();

        foreach ($lines as $line) {
            $group = array_combine($keys, explode(':', $line));
            $group['users'] = '' == $group['users'] ? [] : explode(',', $group['users']);

            $groups->push($group);
        }

        return $groups;
    }

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
