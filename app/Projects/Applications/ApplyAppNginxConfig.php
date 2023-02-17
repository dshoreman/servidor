<?php

namespace Servidor\Projects\Applications;

use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Actions\SyncNginxConfig;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class ApplyAppNginxConfig
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->getApp();
        $project = $event->getProject();

        $step = new ProgressStep('nginx.save', 'Saving nginx config', 50);
        ProjectProgress::dispatch($project, $step->start());

        try {
            (new SyncNginxConfig($app))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (MissingProjectData $_) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));
        }
    }
}
