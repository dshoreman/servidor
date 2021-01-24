<?php

namespace Servidor\Observers;

use Servidor\Projects\Redirect;

class ProjectRedirectObserver
{
    public function saved(Redirect $redirect): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $redirect->project;

        $redirect->writeNginxConfig();

        $project->is_enabled ? $redirect->enable() : $redirect->disable();

        exec('sudo systemctl reload-or-restart nginx.service');
    }
}
