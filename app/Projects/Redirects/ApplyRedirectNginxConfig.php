<?php

namespace Servidor\Projects\Redirects;

use Servidor\Events\ProjectProgress;

class ApplyRedirectNginxConfig
{
    public function handle(ProjectRedirectSaved $event): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $event->redirect->project;

        ProjectProgress::dispatch($project, 'Saving nginx config...');

        $event->redirect->writeNginxConfig();

        $project->is_enabled ? $event->redirect->enable() : $event->redirect->disable();

        ProjectProgress::dispatch($project, ' done.' . PHP_EOL);
    }
}
