<?php

namespace Servidor\Projects\Services\Templates;

use Servidor\Projects\Services\LogFile;

class Laravel extends Php
{
    protected string $publicDir = '/public';

    public function getLogs(): array
    {
        return array_merge(parent::getLogs(), [
            'laravel' => new LogFile($this->service, 'Laravel Log', 'storage/logs/laravel.log'),
        ]);
    }
}
