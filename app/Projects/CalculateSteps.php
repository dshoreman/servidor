<?php

namespace Servidor\Projects;

use Servidor\Projects\Services\ProjectServiceSaving;

class CalculateSteps
{
    public const STEPS = [
        'clone' => 'Cloning project files',
        'enable' => 'Enabling project',
        'nginx.reload' => 'Reloading nginx service',
        'nginx.save' => 'Saving nginx config',
        'nginx.ssl' => 'Saving SSL certificate',
        'user.create' => 'Creating system user',
    ];

    private ?Project $project = null;

    public function handle(ProjectServiceSaving $event): void
    {
        $this->project = $event->getProject();
        $type = $event->getService()->getType();
        $isApp = 'redirect' !== $type;

        $this->trigger('nginx.ssl');

        if ($isApp) {
            $this->trigger('user.create');
        }

        $this->trigger('nginx.save');

        if ($isApp) {
            $this->trigger('clone');
        }

        $this->trigger('enable', 'Enabling ' . $type);
        $this->trigger('nginx.reload');
    }

    private function trigger(string $key, string $text = ''): void
    {
        ProjectProgress::dispatch(
            $this->project,
            new ProgressStep($key, $text ?: self::STEPS[$key], 0),
        );
    }
}
