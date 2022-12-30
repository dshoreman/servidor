<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Applications\LogFile;

class Php extends Html
{
    protected string $nginxTemplate = 'php';

    protected bool $requiresUser = true;

    public function getLogs(): array
    {
        $default = \ini_get('error_log');
        $fallbackPath = '/var/log/php%d.%d-fpm.log';

        $phpErrorLog = $default ?: sprintf($fallbackPath, PHP_MAJOR_VERSION, PHP_MINOR_VERSION);

        return [
            'php' => new LogFile($this->app, 'PHP Error Log', $phpErrorLog),
        ];
    }
}
