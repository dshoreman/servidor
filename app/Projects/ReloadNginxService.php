<?php

namespace Servidor\Projects;

use Servidor\Projects\Redirects\ProjectRedirectSaved;
use Servidor\Projects\Services\ProjectServiceSaved;

class ReloadNginxService
{
    /**
     * @param ProjectRedirectSaved|ProjectServiceSaved $event
     */
    public function handle($event): void
    {
        $step = new ProgressStep('nginx.reload', 'Reloading nginx service', 100);
        ProjectProgress::dispatch($event->getProject(), $step->start());

        exec('sudo systemctl reload-or-restart nginx.service');

        ProjectProgress::dispatch($event->getProject(), $step->complete());
    }
}
