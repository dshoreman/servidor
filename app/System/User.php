<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;
use Servidor\System\Users\LinuxUser;

class User
{
    /**
     * @var LinuxUser
     */
    private $user;

    public function __construct($user)
    {
        $this->user = $user instanceof LinuxUser
                    ? $user : new LinuxUser($user, true);
    }

    public static function find(int $uid): self
    {
        $user = posix_getpwuid($uid);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new self($user);
    }

    public static function findByName(string $username): self
    {
        $user = posix_getpwnam($username);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new self($user);
    }

    private function refresh($nameOrUid): self
    {
        $arr = is_numeric($nameOrUid)
             ? posix_getpwuid((int) $nameOrUid)
             : posix_getpwnam($nameOrUid);

        $this->user = new LinuxUser($arr, true);

        return $this;
    }

    public static function list(): Collection
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
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
        $this->user->setName($data['name'])
                   ->setUid($data['uid'] ?? null)
                   ->setGid($data['gid'] ?? null)
                   ->setGroups($data['groups'] ?? null)
                   ->setMoveHome($data['move_home'] ?? false)
                   ->setHomeDirectory($data['dir'] ?? '');

        if (!$this->user->isDirty()) {
            throw new UserNotModifiedException();
        }

        $this->commit('usermod');

        return $this->refresh($data['uid'] ?? $this->user->uid)->toArray();
    }

    private function commit(string $cmd): self
    {
        $name = $this->user->getOriginal('name');

        exec("sudo {$cmd} {$this->user->toArgs()} {$name}", $output, $retval);
        unset($output);

        if (0 !== $retval) {
            throw new UserSaveException("Something went wrong (exit code: {$retval})");
        }

        return $this;
    }

    public function delete(): void
    {
        exec('sudo userdel ' . $this->user->name);
    }

    public function toArray(): array
    {
        return $this->user->toArray();
    }
}
