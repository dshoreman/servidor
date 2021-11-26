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

    public function pullCode(): bool
    {
        $status = 0;
        $output = [];
        $root = $this->app->source_root;
        $cmd = $this->makePullCommand($root, $this->app->source_branch ?: '');

        if (!is_dir($root)) {
            $dirCmd = 'sudo mkdir -p "%s" && sudo chown www-data:www-data "%s"';
            exec(sprintf($dirCmd, $root, $root), $output, $status);
            exec('whoami');
        }
        if (0 === $status) {
            exec($cmd, $output, $status);
        }

        return 0 === $status;
    }

    private function makePullCommand(string $root, string $branch): string
    {
        if (is_dir($root . '/.git')) {
            return 'cd "' . $root . '"' . (
                $branch ? ' && sudo -u www-data git checkout "' . $branch . '"' : ''
            ) . ' && sudo -u www-data git pull';
        }

        return 'sudo -u www-data git clone' . (
            $branch ? ' --branch "' . $branch . '"' : ''
        ) . ' "' . $this->app->source_uri . '" "' . $root . '"';
    }

    public function requiresUser(): bool
    {
        return $this->requiresUser;
    }
}
