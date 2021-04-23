<?php

namespace Servidor\Projects\Applications;

use Servidor\Events\ProjectProgress;

class ApplyAppNginxConfig
{
    public function handle(ProjectAppSaved $event): void
    {
        if ($event->app->source_repository && '' !== $event->app->domain_name) {
            ProjectProgress::dispatch($event->project, 'Saving nginx config...');

            $event->app->writeNginxConfig();

            ProjectProgress::dispatch($event->project, ' done.' . PHP_EOL);
        }
    }
}
