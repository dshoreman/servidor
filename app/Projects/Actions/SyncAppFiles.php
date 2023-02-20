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
            $this->pull($user, (string) ($this->app->config?->get('source')['branch'] ?? ''));
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

        $this->runCmds($cmds);
    }

    private function pull(string $user, string $branch): void
    {
        $gitExists = is_dir($this->sourcePath . '/.git');
        $branchOpt = $branch ? " --branch '{$branch}'" : '';
        $cmds = ["cd '{$this->sourcePath}'"];

        if ($gitExists && $branch) {
            $cmds[] = "sudo -u {$user} git checkout '{$branch}'";
        }

        $this->runCmds(array_merge($cmds, [
            "sudo -u {$user} git " . (
                $gitExists ? 'pull' : "clone{$branchOpt} '{$this->app->source_uri}' ."
            ),
        ]));
    }

    /**
     * @param array<string> $cmds
     */
    private function runCmds(array $cmds): void
    {
        $this->runShellCmd(implode(' && ', $cmds));
    }

    private function runShellCmd(string $cmd): void
    {
        exec($cmd, $this->output, $this->status);
    }
}
