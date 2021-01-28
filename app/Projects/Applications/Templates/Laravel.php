<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Applications\LogFile;

class Laravel extends Php
{
    /** @var string */
    public $publicDir = '/public';

    public function getLogs(): array
    {
        return array_merge(parent::getLogs(), ['laravel' => new LogFile(
            $this->app,
            'Laravel Log',
            'storage/logs/laravel.log',
        )]);
    }
}
