<?php

namespace Servidor;

class StatsBar
{
    /**
     * @suppress PhanUnextractableAnnotationSuffix
     *
     * @return array{
     *   cpu: float,
     *   load_average: array{'1m': string, '5m': string, '15m': string},
     *   ram: array{total: float, used: float, free: float},
     *   disk: array<string, string>,
     *   hostname: string,
     *   os: array{name: string, distro: string, version: string}
     * }
     */
    public static function stats(): array
    {
        $os = self::parseReleaseFile('os');
        $lsb = self::parseReleaseFile('lsb');
        \assert(isset($os['NAME'], $lsb['DISTRIB_RELEASE']));

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

    /** @return array<string, string> */
    private static function parseReleaseFile(string $file): array
    {
        $flags = FILE_IGNORE_NEW_LINES;
        $data = [];

        foreach (file('/etc/' . $file . '-release', $flags) as $line) {
            [$key, $val] = explode('=', $line);

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
     *
     * @suppress PhanUnextractableAnnotationSuffix
     *
     * @return array{'1m': string, '5m': string, '15m': string}
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
     *
     * @return array{total: float, used: float, free: float}
     */
    private static function getRamUsage(): array
    {
        $output = exec('free | tail -n+2 | head -n1');

        /** @var array{string, int, int, int, int, int} $data */
        $data = sscanf($output, '%s %d %d %d %d %d');

        return [
            'total' => round($data[1] / 1024),
            'used' => round($data[2] / 1024),
            'free' => round((round($data[3]) + round($data[5])) / 1024),
        ];
    }

    /**
     * Get the disk usage details for the root mountpoint.
     *
     * @return array<string, string>
     */
    private static function getDiskUsage(): array
    {
        $output = exec('df | grep " /$"');

        /** @var array{string, int, int, int, string, string} $data */
        $data = sscanf($output, '%s %d %d %d %s %s');

        return [
            'partition' => $data[0],
            'total' => number_format($data[1] / 1024 / 1024, 1),
            'used' => number_format($data[2] / 1024 / 1024, 1),
            'used_pct' => $data[4],
            'free' => number_format($data[3] / 1024 / 1024, 1),
        ];
    }
}
