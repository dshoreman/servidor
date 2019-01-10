<?php namespace Servidor;

class StatsBar
{
    public static function stats()
    {
        $os = self::parseReleaseFile('os');
        $lsb = self::parseReleaseFile('lsb');

        return [
            'hostname' => gethostname(),
            'os' => [
                'name' => php_uname('s'),
                'distro' => $os['NAME'],
                'version' => $lsb['DISTRIB_RELEASE'],
            ],
        ];
    }

    protected static function parseReleaseFile($file)
    {
        $flags = FILE_IGNORE_NEW_LINES;
        $data = [];

        foreach (file('/etc/'.$file.'-release', $flags) as $line) {
            list ($key, $val) = explode('=', $line);

            $key = trim($key, '[]');
            $val = trim($val, '"');

            $data[$key] = $val;
        }

        return $data;
    }
}
