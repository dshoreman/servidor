<?php

namespace Servidor;

class StatsBar
{
    public static function stats(): array
    {
        $os = self::parseReleaseFile('os');
        $lsb = self::parseReleaseFile('lsb');

        return [
            'cpu' => self::getCpuUsage(),
            'load_average' => self::getLoadAverage(),
            'ram' => self::getRamUsage(),
            'disk' => self::getDiskUsage(),
            'hostname' => gethostname(),
            'os' => [
                'name' => php_uname('s'),
                'distro' => $os['NAME'],
                'version' => $lsb['DISTRIB_RELEASE'],
            ],
        ];
    }

    private static function parseReleaseFile(string $file): array
    {
        $flags = FILE_IGNORE_NEW_LINES;
        $data = [];

        foreach (file('/etc/' . $file . '-release', $flags) as $line) {
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

    /**
     * Get the CPU load average over 1, 5 and 15 minute intervals.
     */
    private static function getLoadAverage(): array
    {
        $output = explode(' ', exec('awk \'{ print $1,$2,$3; }\' /proc/loadavg'));

        return [
            '1m' => $output[0],
            '5m' => $output[1],
            '15m' => $output[2],
        ];
    }

    /**
     * Get details about the RAM currently used/free.
     */
    private static function getRamUsage(): array
    {
        $output = exec('free | tail -n+2 | head -n1');

        $data = sscanf($output, '%s %d %d %d %d %d');

        return [
            'total' => round((float) $data[1] / 1024),
            'used' => round((float) $data[2] / 1024),
            'free' => round((round((float) $data[3]) + round((float) $data[5])) / 1024),
        ];
    }

    /**
     * Get the disk usage details for the root mountpoint.
     */
    private static function getDiskUsage(): array
    {
        $output = exec('df | grep " /$"');

        $data = sscanf($output, '%s %d %d %d %s %s');

        return [
            'partition' => $data[0],
            'total' => number_format((float) $data[1] / 1024 / 1024, 1),
            'used' => number_format((float) $data[2] / 1024 / 1024, 1),
            'used_pct' => $data[4],
            'free' => number_format((float) $data[3] / 1024 / 1024, 1),
        ];
    }
}
