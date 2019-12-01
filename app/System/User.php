<?php

namespace Servidor\System;

use Exception;
use Illuminate\Support\Collection;

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
            return null;
        }

        return new self($user);
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
            throw new Exception("Something went wrong (Exit code: {$retval})");
        }

        return posix_getpwnam($name);
    }

    public function delete(): void
    {
        exec('sudo userdel ' . $this->user['name']);
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
}
