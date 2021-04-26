<?php

namespace Servidor\Projects;

use Servidor\Events\ProjectProgress;
use Servidor\Projects\Applications\ProjectAppSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaved;

class ReloadNginxService
{
    /**
     * @param ProjectAppSaved|ProjectRedirectSaved $event
     */
    public function handle($event): void
    {
        $step = new ProgressStep('nginx.reload', 'Reloading nginx service', 100);
        ProjectProgress::dispatch($event->project, $step);

        exec('sudo systemctl reload-or-restart nginx.service');

        ProjectProgress::dispatch($event->project, $step->complete());
    }
}
