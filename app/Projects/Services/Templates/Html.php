<?php

namespace Servidor\Projects\Services\Templates;

use Servidor\Projects\ProjectService;
use Servidor\Projects\RequiresNginxData;

class Html implements Template
{
    use RequiresNginxData;

    protected ProjectService $service;

    protected string $nginxTemplate = 'basic';

    protected string $publicDir = '';

    /**
     * @var array<string, mixed>
     */
    protected array $requiredNginxData = [
        'domain_name' => 'domain name',
        'config.source.repository' => 'source repo',
    ];

    protected bool $requiresUser = false;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
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

    public function serviceType(): string
    {
        return 'website';
    }
}
