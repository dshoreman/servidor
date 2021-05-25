<?php

namespace Servidor\Projects\Applications;

use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class ApplyAppNginxConfig
{
    public function handle(ProjectAppSaved $event): void
    {
        $app = $event->getApp();
        $project = $event->getProject();

        $step = new ProgressStep('nginx.save', 'Saving nginx config', 50);
        ProjectProgress::dispatch($project, $step);

        if (!$app->source_repository || '' === $app->domain_name) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));

            return;
        }

        $app->writeNginxConfig();

        ProjectProgress::dispatch($project, $step->complete());
    }
}
