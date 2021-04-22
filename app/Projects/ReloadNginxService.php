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
        ProjectProgress::dispatch($event->project, 'Reloading nginx service...');

        exec('sudo systemctl reload-or-restart nginx.service');
    }
}
