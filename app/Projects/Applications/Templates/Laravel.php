<?php

namespace Servidor\Projects\Applications\Templates;

class Laravel extends Php
{
    public function getLogPaths(): array
    {
        return array_merge(parent::getLogPaths(), [[
            'name' => 'laravel',
            'title' => 'Laravel Log',
            'path' => 'storage/logs/laravel.log',
        ]]);
    }
}
