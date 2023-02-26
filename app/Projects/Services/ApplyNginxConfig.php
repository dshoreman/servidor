<?php

namespace Servidor\Projects\Services;

use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Actions\SyncNginxConfig;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class ApplyNginxConfig
{
    public function handle(ProjectServiceSaved $event): void
    {
        $service = $event->getService();
        $project = $event->getProject();
        $progress = 'redirect' === $service->getType() ? 60 : 50;

        $step = new ProgressStep('nginx.save', 'Saving nginx config', $progress);
        ProjectProgress::dispatch($project, $step->start());

        try {
            (new SyncNginxConfig($service))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (MissingProjectData $_) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));
        }
    }
}
