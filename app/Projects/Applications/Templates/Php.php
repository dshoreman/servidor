<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Applications\LogFile;

class Php extends Html
{
    public $nginxTemplate = 'php';

    public function getLogs(): array
    {
        return ['php' => new LogFile($this->app, 'PHP Error Log', ini_get('error_log') ?: sprintf(
            '/var/log/php%d.%d-fpm.log',
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
        ))];
    }

    public function requiresUser(): bool
    {
        return true;
    }
}
