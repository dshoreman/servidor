<?php

namespace Servidor\Projects\Applications;

use Servidor\Events\ProjectProgress;
use Servidor\Projects\ProgressStep;

class DeployApp
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->app;
        $project = $event->project;

        ProjectProgress::dispatch($project, $step = new ProgressStep('clone', 'Cloning project files'));

        if ($app->source_repository && $app->domain_name) {
            if ($project->is_enabled) {
                $app->template()->pullCode(true);

                ProjectProgress::dispatch($project, $step->complete());

                return;
            }

            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_NOT_ENABLED));
            ProjectProgress::dispatch($project, $step = new ProgressStep('disable', 'Disabling project'));

            $app->template()->disable();

            ProjectProgress::dispatch($project, $step->complete());
        }
    }
}
