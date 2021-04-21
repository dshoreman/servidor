<?php

namespace Servidor\Projects\Applications;

class DeployApp
{
    public function handle(ProjectAppSaved $event): void
    {
        /** @var \Servidor\Projects\Project */
        $project = $event->app->project;

        if ($event->app->source_repository && $event->app->domain_name) {
            if ($project->is_enabled) {
                $event->app->template()->pullCode(true);

                return;
            }

            $event->app->template()->disable();
        }
    }
}
