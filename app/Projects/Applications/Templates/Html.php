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

    public function pullCode()
    {
        $status = 0;
        $output = [];
        $root = $this->app->source_root;
        $branch = $this->app->source_branch;

        if (is_dir($root . '/.git')) {
            $args = $branch ? ' && git checkout "' . $branch . '"' : '';

            $cmd = 'cd "' . $root . '"' . $args . ' && git pull';
        } else {
            $args = $branch ? ' --branch "' . $branch . '"' : '';
            $paths = ' "' . $this->app->source_uri . '" "' . $root . '"';

            $cmd = 'git clone' . $args . $paths;
        }

        if (!is_dir($root)) {
            $dirCmd = 'sudo mkdir -p "%s" && sudo chown www-data:www-data "%s"';
            exec(sprintf($dirCmd, $root, $root), $output, $status);
        }
        if (0 === $status) {
            exec($cmd, $output, $status);
        }

        return 0 === $status;
    }

    public function requiresUser(): bool
    {
        return false;
    }
}
