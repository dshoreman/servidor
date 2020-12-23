<?php

namespace Servidor\Projects\Applications\Templates;

class Php
{
    public function getLogPaths(): array
    {
        return [[
            'name' => 'php',
            'title' => 'PHP Error Log',
            'path' => ini_get('error_log') ?: sprintf(
                '/var/log/php%d.%d-fpm.log',
                PHP_MAJOR_VERSION,
                PHP_MINOR_VERSION,
            ),
        ]];
    }
}
