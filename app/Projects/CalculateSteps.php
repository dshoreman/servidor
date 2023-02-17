<?php

namespace Servidor\Projects;

use Servidor\Projects\Applications\ProjectAppSaving;
use Servidor\Projects\Redirects\ProjectRedirectSaving;

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

    public function handle(ProjectAppSaving|ProjectRedirectSaving $event): void
    {
        $this->project = $event->getProject();

        if ($event instanceof ProjectAppSaving) {
            $this->triggerAppEvents();
        }

        if ($event instanceof ProjectRedirectSaving) {
            $this->triggerRedirectEvents();
        }
    }

    private function triggerAppEvents(): void
    {
        $this->trigger('nginx.ssl');
        $this->trigger('user.create');
        $this->trigger('nginx.save');
        $this->trigger('clone');
        $this->trigger('enable', 'Enabling application');
        $this->trigger('nginx.reload');
    }

    private function triggerRedirectEvents(): void
    {
        $this->trigger('nginx.ssl');
        $this->trigger('nginx.save');
        $this->trigger('enable', 'Enabling redirect');
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
