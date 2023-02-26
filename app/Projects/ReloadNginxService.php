<?php

namespace Servidor\Projects;

use Servidor\Projects\Services\ProjectServiceSaved;

class ReloadNginxService
{
    public function handle(ProjectSaved|ProjectServiceSaved $event): void
    {
        $progress = $event instanceof ProjectSaved ? 5 : 100;

        $step = new ProgressStep('nginx.reload', 'Reloading nginx service', $progress);
        ProjectProgress::dispatch($event->getProject(), $step->start());

        exec('sudo systemctl reload-or-restart nginx.service');

        ProjectProgress::dispatch($event->getProject(), $step->complete());
    }
}
