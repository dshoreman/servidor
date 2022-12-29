<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\System\Groups\GenericGroupSaveFailure;
use Servidor\System\Groups\GroupNotFound;
use Servidor\System\Groups\GroupNotModified;
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
     * @var array<int, string>
     */
    private array $errors;

    /**
     * @param array<string, mixed>|LinuxGroup $group
     */
    public function __construct(array|LinuxGroup $group)
    {
        $this->group = $group instanceof LinuxGroup
                     ? $group : new LinuxGroup($group);

        $this->errors = [
            self::GROUP_GID_TAKEN => "The group's GID must be unique",
            self::GROUP_NAME_TAKEN => 'The group name must be unique',
            self::GROUP_OPTION_INVALID => 'Invalid argument to option',
            self::GROUP_SYNTAX_INVALID => 'Invalid command syntax.',
        ];
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
        $error = 'Something unexpected happened!';
        $retval = $this->commit('groupadd');
        $visibleExitStatus = $retval;

        if (0 === $retval) {
            return $this;
        }

        if (isset($this->errors[$retval])) {
            $error = $this->errors[$retval];
            $visibleExitStatus = 0;
        }

        throw new GenericGroupSaveFailure($error, $visibleExitStatus);
    }

    private function commitMod(): self
    {
        if ($this->group->hasArgs()) {
            $retval = $this->commit('groupmod');

            if (0 !== $retval) {
                throw new GenericGroupSaveFailure("Couldn't update the group.", $retval);
            }
        }

        $this->updateUsers();

        return $this;
    }

    private function updateUsers(): void
    {
        if (!$this->group->hasChangedUsers()) {
            return;
        }

        $users = $this->group->users;

        $this->refresh($this->group->gid ?? $this->group->name);
        $this->group->setUsers($users);

        $retval = $this->commit('gpasswd', '-M "' . implode(',', $this->group->users) . '"');

        if (0 !== $retval) {
            throw new GenericGroupSaveFailure("Couldn't update the group's users.", $retval);
        }
    }

    /** @return array<string, mixed> */
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
            throw new GroupNotFound();
        }

        return new self($group);
    }

    /**
     * @psalm-return Collection<int<0, max>, array{
     *   name: string, password: string, gid: string, users: list<string>
     * }>
     *
     * @phpstan-return Collection<int, array{
     *   name: string, password: string, gid: string, users: list<string>
     * }>
     */
    public static function list(): Collection
    {
        $groups = [];
        exec('cat /etc/group', $lines);

        foreach ($lines as $line) {
            \assert(\is_string($line));
            $line = explode(':', $line);

            $groups[] = [
                'name' => $line[0],
                'password' => $line[1],
                'gid' => $line[2],
                'users' => '' === $line[3] ? [] : explode(',', $line[3]),
            ];
        }

        return collect($groups);
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<string, mixed>
     */
    public function update(array $data): array
    {
        /** @var array<string>|null $users */
        $users = isset($data['users']) ? (array) $data['users'] : null;

        $this->group
            ->setName((string) $data['name'])
            ->setGid(isset($data['gid']) ? (int) $data['gid'] : null)
            ->setUsers($users)
        ;

        if (!$this->group->isDirty()) {
            throw new GroupNotModified();
        }

        $this->commitMod();

        return $this->refresh($this->group->gid ?? $this->group->name)->toArray();
    }

    /** @param int|string $nameOrGid */
    private function refresh($nameOrGid): self
    {
        /** @var array<string, mixed>|false $arr */
        $arr = \is_int($nameOrGid)
             ? posix_getgrgid($nameOrGid)
             : posix_getgrnam($nameOrGid);
        \assert(false !== $arr);

        $this->group = new LinuxGroup($arr);

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->group->toArray();
    }
}
