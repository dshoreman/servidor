<?php

namespace Servidor\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Servidor\Projects\Domainable;
use Servidor\Traits\TogglesNginxConfigs;

class Html implements Template, Domainable
{
    use TogglesNginxConfigs;

    /** @var Application */
    protected $app;

    /** @var string */
    public $nginxTemplate = 'basic';

    /** @var string */
    public $publicDir = '';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getApp(): Application
    {
        return $this->app;
    }

    public function domainName(): string
    {
        return $this->getApp()->domain_name ?? '';
    }

    public function getLogs(): array
    {
        return [];
    }

    public function pullCode(bool $autoEnable = false): bool
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
        if ($autoEnable && 0 === $status) {
            $this->enable();
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
        return false;
    }
}
