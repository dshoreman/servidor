<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Servidor\Projects\RequiresNginxData;

class Html implements Template
{
    use RequiresNginxData;

    protected Application $app;

    protected string $nginxTemplate = 'basic';

    protected string $publicDir = '';

    protected array $requiredNginxData = [
        'domain_name' => 'domain name',
        'source_repository' => 'source repo',
    ];

    protected bool $requiresUser = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function getLogs(): array
    {
        return [];
    }

    public function nginxTemplate(): string
    {
        return $this->nginxTemplate;
    }

    public function publicDir(): string
    {
        return $this->publicDir;
    }

    public function requiresUser(): bool
    {
        return $this->requiresUser;
    }
}
