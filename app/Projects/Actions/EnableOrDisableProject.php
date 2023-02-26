<?php

namespace Servidor\Projects\Actions;

use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class EnableOrDisableProject
{
    public const PATH_AVAILABLE = '/etc/nginx/sites-available/';
    public const PATH_ENABLED = '/etc/nginx/sites-enabled/';

    private string $configFile;

    public function __construct(
        private ProjectService $service,
    ) {
        $service->checkNginxData();

        $this->configFile = $this->service->domain_name . '.conf';
    }

    public function execute(): void
    {
        \assert($this->service->project instanceof Project);
        $target = self::PATH_AVAILABLE . $this->configFile;
        $symlink = self::PATH_ENABLED . $this->configFile;

        $isEnabled = is_link($symlink) && readlink($symlink) === $target;
        $shouldBeEnabled = $this->service->project->is_enabled;

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
