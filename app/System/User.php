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
     * @param array|LinuxUser $user
     */
    public function __construct($user)
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
        $arr = is_numeric($nameOrUid)
             ? posix_getpwuid((int) $nameOrUid)
             : posix_getpwnam($nameOrUid);

        $this->user = new LinuxUser((array) $arr, true);

        return $this;
    }

    public static function list(): Collection
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
            assert(is_string($line));

            $user = new self(
                array_combine($keys, explode(':', $line)),
            );

            $users->push($user->toArray());
        }

        return $users;
    }

    public static function create(string $name, ?int $uid = null, ?int $gid = null): array
    {
        return self::createCustom(
            (new LinuxUser(['name' => $name]))
                ->setUid($uid ?: null)
                ->setGid($gid ?: null),
        );
    }

    public static function createCustom(LinuxUser $user): array
    {
        $name = $user->name;
        $user = new self($user);

        $user->commit('useradd');

        return $user->refresh($name)->toArray();
    }

    public function update(array $data): array
    {
        $this->user
            ->setName((string) $data['name'])
            ->setUid(isset($data['uid']) ? (int) $data['uid'] : null)
            ->setGid(isset($data['gid']) ? (int) $data['gid'] : null)
            ->setShell((string) ($data['shell'] ?? ''))
            ->setGroups(isset($data['groups']) ? (array) $data['groups'] : null)
            ->setMoveHome((bool) ($data['move_home'] ?? false))
            ->setHomeDirectory((string) ($data['dir'] ?? ''));

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

    public function toArray(): array
    {
        return $this->user->toArray();
    }
}
