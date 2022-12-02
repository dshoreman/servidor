<?php

namespace Servidor\Projects\Redirects;

use Servidor\Projects\Actions\SyncNginxConfig;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class ApplyRedirectNginxConfig
{
    public function handle(ProjectRedirectSaved $event): void
    {
        $step = new ProgressStep('nginx.save', 'Saving nginx config', 60);
        ProjectProgress::dispatch($event->getProject(), $step->start());

        (new SyncNginxConfig($event->getRedirect()))->execute();

        ProjectProgress::dispatch($event->getProject(), $step->complete());
    }
}
