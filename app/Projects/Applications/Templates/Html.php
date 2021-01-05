<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Application;

class Html
{
    protected Application $app;

    public string $publicDir = '';

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
