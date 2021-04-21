<?php

namespace Servidor\Projects\Applications;

class ApplyAppNginxConfig
{
    public function handle(ProjectAppSaved $event): void
    {
        if ($event->app->source_repository && '' !== $event->app->domain_name) {
            $event->app->writeNginxConfig();
        }
    }
}
