<?php

namespace Servidor;

class StatsBar
{
    public static function stats()
    {
        $os = self::parseReleaseFile('os');
        $lsb = self::parseReleaseFile('lsb');

        return [
            'cpu' => self::getCpuUsage(),
            'hostname' => gethostname(),
            'os' => [
                'name' => php_uname('s'),
                'distro' => $os['NAME'],
                'version' => $lsb['DISTRIB_RELEASE'],
            ],
        ];
    }

    private static function parseReleaseFile($file)
    {
        $flags = FILE_IGNORE_NEW_LINES;
        $data = [];

        foreach (file('/etc/'.$file.'-release', $flags) as $line) {
            list($key, $val) = explode('=', $line);

            $key = trim($key, '[]');
            $val = trim($val, '"');

            $data[$key] = $val;
        }

        return $data;
    }

    /**
     * Get the current CPU usage in percent.
     */
    private static function getCpuUsage(): float
    {
        return (float) exec("mpstat | tail -n1 | awk '{ print 100 - $12 }'");
    }
}
