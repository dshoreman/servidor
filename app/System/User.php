<?php

namespace Servidor\System;

use Illuminate\Support\Collection;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;

class User
{
    private $user = [];

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public static function find(int $uid): self
    {
        $user = posix_getpwuid($uid);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return new self($user);
    }

    private function refresh($uid): self
    {
        $this->user = posix_getpwuid($uid);

        return $this;
    }

    public static function list(): Collection
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
            $user = array_combine($keys, explode(':', $line));
            $user['groups'] = (new self($user))->secondaryGroups();

            $users->push($user);
        }

        return $users;
    }

    public static function create(string $name, int $uid = null, int $gid = null): array
    {
        if ($uid > 0) {
            $options[] = '-u ' . $uid;
        }

        if ($gid > 0) {
            $options[] = '-g ' . $gid;
        }

        // TODO: Add handling for secondary groups (`-G group1 group2 ...`)

        $options[] = $name;

        exec('sudo useradd ' . implode(' ', $options), $output, $retval);
        unset($output);

        if (0 !== $retval) {
            throw new UserSaveException("Something went wrong (exit code: {$retval})");
        }

        return posix_getpwnam($name);
    }

    public function update(array $data): self
    {
        $options = [];
        $uid = $this->user['uid'];

        if ($data['name'] != $this->user['name']) {
            $options[] = '-l ' . $data['name'];
        }

        if (isset($data['uid']) && $data['uid'] != $this->user['uid'] && (int) $data['uid'] > 0) {
            $uid = (int) $data['uid'];
            $options[] = '-u ' . $uid;
        }

        if ($data['gid'] != $this->user['gid'] && (int) $data['gid'] > 0) {
            $options[] = '-g ' . (int) $data['gid'];
        }

        $this->loadSecondaryGroups();

        if (isset($data['groups']) && $data['groups'] != $this->user['groups']) {
            $options[] = '-G "' . implode(',', $data['groups']) . '"';
        }

        if (empty($options)) {
            throw new UserNotModifiedException();
        }

        $options[] = $this->user['name'];

        exec('sudo usermod ' . implode(' ', $options), $output, $retval);
        unset($output);

        if (0 !== $retval) {
            throw new UserSaveException("Something went wrong (exit code: {$retval})");
        }

        $this->refresh($uid)->loadSecondaryGroups();

        return $this;
    }

    public function delete(): void
    {
        exec('sudo userdel ' . $this->user['name']);
    }

    private function loadSecondaryGroups(): self
    {
        $this->user['groups'] = $this->secondaryGroups();

        return $this;
    }

    public function secondaryGroups(): array
    {
        $groups = [];
        $primary = explode(':', exec('getent group ' . $this->user['gid']));
        $effective = explode(' ', exec('groups ' . $this->user['name'] . " | sed 's/.* : //'"));

        $primaryName = reset($primary);
        $primaryMembers = explode(',', end($primary));

        foreach ($effective as $group) {
            if ($group == $primaryName && !in_array($group, $primaryMembers)) {
                continue;
            }

            $groups[] = $group;
        }

        return $groups;
    }

    public function toArray(): array
    {
        return $this->user;
    }
}
