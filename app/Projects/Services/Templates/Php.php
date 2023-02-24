<?php

namespace Servidor\Projects\Services\Templates;

use Servidor\Projects\Services\LogFile;

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
            'php' => new LogFile($this->service, 'PHP Error Log', $phpErrorLog),
        ];
    }
}
