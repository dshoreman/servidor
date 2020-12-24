<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Servidor\Projects\Applications\LogFile;

class Php
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getLogs(): array
    {
        return ['php' => new LogFile($this->app, 'PHP Error Log', ini_get('error_log') ?: sprintf(
            '/var/log/php%d.%d-fpm.log',
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
        ))];
    }
}
