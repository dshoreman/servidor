<?php

namespace Servidor\Projects\Services\Templates;

use Servidor\Projects\ProjectService;
use Servidor\Projects\RequiresNginxData;

class Redirect implements Template
{
    use RequiresNginxData;

    /**
     * @var array<string, string>
     */
    protected array $requiredNginxData = [
        'config.redirect.target' => 'target',
        'domain_name' => 'domain name',
    ];

    public function __construct(
        protected ProjectService $service,
    ) {
    }

    public function getService(): ProjectService
    {
        return $this->service;
    }

    public function getLogs(): array
    {
        return [];
    }

    public function nginxTemplate(): string
    {
        return 'redirect';
    }

    public function publicDir(): string
    {
        return '';
    }

    public function requiresUser(): bool
    {
        return false;
    }

    public function serviceType(): string
    {
        return 'redirect';
    }
}
