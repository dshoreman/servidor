<?php

namespace Servidor\Projects\Actions;

use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class EnableOrDisableProject
{
    public const PATH_AVAILABLE = '/etc/nginx/sites-available/';
    public const PATH_ENABLED = '/etc/nginx/sites-enabled/';

    private string $configFile;

    private Project $project;

    public function __construct(
        private Application|Redirect $appOrRedirect,
    ) {
        $appOrRedirect->checkNginxData();

        $this->configFile = $this->appOrRedirect->domain_name . '.conf';

        \assert($this->appOrRedirect->project instanceof Project);
        $this->project = $this->appOrRedirect->project;
    }

    public function execute(): void
    {
        $target = self::PATH_AVAILABLE . $this->configFile;
        $symlink = self::PATH_ENABLED . $this->configFile;

        $isEnabled = is_link($symlink) && readlink($symlink) === $target;
        $shouldBeEnabled = $this->project->is_enabled;

        if ($isEnabled === $shouldBeEnabled) {
            return;
        }
        if (file_exists($symlink)) {
            exec(sprintf('sudo rm "%s"', $symlink));
            clearstatcache(true, $symlink);
        }
        if ($shouldBeEnabled) {
            exec(sprintf('sudo ln -s "%s" "%s"', $target, $symlink));
        }
    }
}
