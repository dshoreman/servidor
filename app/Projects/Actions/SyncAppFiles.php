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
        $app->checkNginxData();

        $this->sourcePath = $app->source_root;
    }

    public function execute(): void
    {
        $user = $this->app->template()->requiresUser()
              ? (string) ($this->app->system_user['name'] ?? '')
              : 'www-data';

        if (!is_dir($this->sourcePath)) {
            $this->createProjectDir($user, $this->sourcePath);
        }

        if (0 === $this->status) {
            $this->runShellCmd($this->pull($user, $this->app->source_branch ?: ''));
        }
    }

    private function createProjectDir(string $user, string $path): void
    {
        $cmds = [
            "sudo mkdir -p '{$path}'",
            "sudo chown {$user}:www-data '{$path}'",
            "sudo chmod g+s {$path}",
        ];

        if ('www-data' !== $user) {
            $cmds[] = "sudo chmod o+x '" . \dirname($path) . "'";
        }

        $this->runShellCmd(implode(' && ', $cmds));
    }

    private function pull(string $user, string $branch): string
    {
        if (is_dir($this->sourcePath . '/.git')) {
            $action = $branch ? "checkout \"{$branch}\"" : 'pull';

            return sprintf('cd "%s" && sudo -u %s git %s', $user, $this->sourcePath, $action);
        }

        return sprintf(
            'sudo -u %s git clone %s"%s" "%s"',
            $user,
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
