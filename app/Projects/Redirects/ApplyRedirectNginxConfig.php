<?php

namespace Servidor\Projects\Redirects;

class ApplyRedirectNginxConfig
{
    public function handle(ProjectRedirectSaved $event): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $event->redirect->project;

        $event->redirect->writeNginxConfig();

        $project->is_enabled ? $event->redirect->enable() : $event->redirect->disable();
    }
}
