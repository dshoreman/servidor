<?php

namespace Servidor\Projects\Redirects;

use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class ApplyRedirectNginxConfig
{
    public function handle(ProjectRedirectSaved $event): void
    {
        $project = $event->getProject();
        $redirect = $event->getRedirect();

        $step = new ProgressStep('nginx.save', 'Saving nginx config', 70);
        ProjectProgress::dispatch($project, $step);

        $redirect->writeNginxConfig();

        $project->is_enabled ? $redirect->enable() : $redirect->disable();

        ProjectProgress::dispatch($project, $step->complete());
    }
}
