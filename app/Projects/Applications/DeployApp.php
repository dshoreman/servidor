<?php

namespace Servidor\Projects\Applications;

use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class DeployApp
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->getApp();
        $project = $event->getProject();

        $step = new ProgressStep('clone', 'Cloning project files', 85);
        ProjectProgress::dispatch($project, $step);

        if ($app->source_repository && $app->domain_name) {
            if ($project->is_enabled) {
                $app->template()->pullCode();

                ProjectProgress::dispatch($project, $step->complete());

                return;
            }

            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_NOT_ENABLED));
        }
    }
}
