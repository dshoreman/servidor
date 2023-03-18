<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\System\Groups\GenericUserSaveFailure;
use Servidor\System\Users\LinuxUser;
use Servidor\System\Users\UserNotFound;
use Servidor\System\Users\UserNotModified;

class User
{
    /**
     * @var LinuxUser
     */
    private $user;

    /**
     * @param array<string, mixed>|LinuxUser $user
     */
    public function __construct(array|LinuxUser $user)
    {
        $this->user = $user instanceof LinuxUser
                    ? $user : new LinuxUser($user, true);
    }

    public static function find(int $uid): self
    {
        $user = posix_getpwuid($uid);

        if (!$user) {
            throw new UserNotFound();
        }

        return new self($user);
    }

    public static function findByName(string $username): self
    {
        $user = posix_getpwnam($username);

        if (!$user) {
            throw new UserNotFound();
        }

        return new self($user);
    }

    /**
     * @param int|string $nameOrUid
     */
    private function refresh($nameOrUid): self
    {
        /** @var array<string, mixed>|false $arr */
        $arr = is_numeric($nameOrUid)
             ? posix_getpwuid((int) $nameOrUid)
             : posix_getpwnam($nameOrUid);
        \assert(false !== $arr);

        $this->user = new LinuxUser($arr, true);

        return $this;
    }

    /**
     * @phpstan-return Collection<int, array{
     *   name: string, dir: string, groups: array<string>, shell: string, gid: ?int, uid: ?int
     * }>
     *
     * @psalm-return Collection<int<0, max>, array{
     *   name: string, dir: string, groups: array<string>, shell: string, gid: ?int, uid: ?int
     * }>
     */
    public static function list(): Collection
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
        $users = [];

        /** @var array<string> $lines */
        foreach ($lines as $line) {
            $user = new self(
                array_combine($keys, explode(':', $line)),
            );

            $users[] = $user->toArray();
        }

        return collect($users);
    }

    /** @return array<string, mixed> */
    public static function create(string $name, ?int $uid = null, ?int $gid = null): array
    {
        return self::createCustom(
            (new LinuxUser(['name' => $name]))
                ->setUid($uid ?: null)
                ->setGid($gid ?: null),
        );
    }

    /** @return array<string, mixed> */
    public static function createCustom(LinuxUser $user): array
    {
        $name = $user->name;
        $user = new self($user);

        $user->commit('useradd');

        return $user->refresh($name)->toArray();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function update(array $data): array
    {
        /** @var array<string>|null $groups */
        $groups = $data['groups'] ?? null;

        $this->user
            ->setName((string) $data['name'])
            ->setUid(isset($data['uid']) ? (int) $data['uid'] : null)
            ->setGid(isset($data['gid']) ? (int) $data['gid'] : null)
            ->setShell((string) ($data['shell'] ?? ''))
            ->setGroups($groups)
            ->setMoveHome((bool) ($data['move_home'] ?? false))
            ->setHomeDirectory((string) ($data['dir'] ?? ''))
        ;

        if (!$this->user->isDirty()) {
            throw new UserNotModified();
        }

        $this->commit('usermod');

        return $this->refresh($this->user->uid ?? $this->user->name)->toArray();
    }

    private function commit(string $cmd): self
    {
        $name = (string) $this->user->getOriginal('name');

        exec("sudo {$cmd} {$this->user->toArgs()} {$name}", $_, $retval);

        if (0 !== $retval) {
            throw new GenericUserSaveFailure("Something went wrong (exit code: {$retval})");
        }

        return $this;
    }

    public function delete(bool $withHome = false): void
    {
        $cmd = 'sudo userdel ';

        if ($withHome) {
            $cmd .= '--remove ';
        }

        exec($cmd . $this->user->name);
    }

    /** @return array{name: string, dir: string, groups: array<string>, shell: string, gid: ?int, uid: ?int} */
    public function toArray(): array
    {
        return $this->user->toArray();
    }
}
