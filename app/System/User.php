<?php

namespace Servidor\System;

use Exception;

class User
{
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
}
