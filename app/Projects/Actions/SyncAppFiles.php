<?php

namespace Servidor\Projects\Actions;

use Servidor\Projects\Application;

class SyncAppFiles
{
    private string $sourcePath = '';

    private array $output = [];

    private int $status = 0;

    public function __construct(
        public Application $app,
    ) {
        $this->sourcePath = $app->source_root;
    }

    public function execute(): void
    {
        if (!is_dir($this->sourcePath)) {
            $cmd = 'sudo mkdir -p "%s" && sudo chown www-data:www-data "%s"';

            $this->runShellCmd(sprintf($cmd, $this->sourcePath, $this->sourcePath));
        }
        if (0 === $this->status) {
            $this->runShellCmd($this->pull($this->app->source_branch ?: ''));
        }
    }

    private function pull(string $branch): string
    {
        if (is_dir($this->sourcePath . '/.git')) {
            $action = $branch ? "checkout \"{$branch}\"" : 'pull';

            return sprintf('cd "%s" && sudo -u www-data git %s', $this->sourcePath, $action);
        }

        return sprintf(
            'sudo -u www-data git clone %s"%s" "%s"',
            $branch ? "--branch \"{$branch}\" " : '',
            $this->app->source_uri,
            $this->sourcePath,
        );
    }

    private function runShellCmd(string $cmd): void
    {
        exec($cmd, $this->output, $this->status);
    }
}
