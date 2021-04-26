<?php

namespace Servidor\Projects\Applications;

use Servidor\Events\ProjectProgress;
use Servidor\Projects\ProgressStep;

class ApplyAppNginxConfig
{
    public function handle(ProjectAppSaved $event): void
    {
        $step = new ProgressStep('nginx.save', 'Saving nginx config', 35);
        ProjectProgress::dispatch($event->project, $step);

        if (!$event->app->source_repository || '' === $event->app->domain_name) {
            ProjectProgress::dispatch($event->project, $step->skip(ProgressStep::REASON_MISSING_DATA));

            return;
        }

        $event->app->writeNginxConfig();

        ProjectProgress::dispatch($event->project, $step->complete());
    }
}
