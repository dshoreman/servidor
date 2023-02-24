<?php

namespace Servidor\Projects\Services;

use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Actions\SyncAppFiles;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class DeployApp
{
    public function handle(ProjectServiceSaved $event): void
    {
        $service = $event->getService();
        $project = $event->getProject();

        $step = new ProgressStep('clone', 'Cloning project files', 85);
        ProjectProgress::dispatch($project, $step->start());

        if (!$project->is_enabled) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_NOT_ENABLED));

            return;
        }

        try {
            (new SyncAppFiles($service))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (MissingProjectData $_) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));
        }
    }
}
