<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Application;

class Html
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getLogs(): array
    {
        return [];
    }

    public function requiresUser(): bool
    {
        return false;
    }
}
