<?php

namespace Servidor\Projects\Applications;

use Servidor\Events\ProjectProgress;

class DeployApp
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->app;
        $project = $event->project;

        if ($app->source_repository && $app->domain_name) {
            if ($project->is_enabled) {
                ProjectProgress::dispatch($project, 'Cloning project repo...');

                $app->template()->pullCode(true);

                ProjectProgress::dispatch($project, ' done.' . PHP_EOL);

                return;
            }

            ProjectProgress::dispatch($project, 'Not flagged for deploy, skipping clone and disabling project...');

            $app->template()->disable();

            ProjectProgress::dispatch($project, ' done.' . PHP_EOL);
        }
    }
}
