<?php

namespace Servidor\Projects;

use Servidor\Projects\Applications\ProjectAppSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaved;

class ToggleProjectVisibility
{
    public function handle(ProjectAppSaved|ProjectRedirectSaved $event): void
    {
        if ($event instanceof ProjectRedirectSaved) {
            $redirect = $event->getRedirect();
            $event->getProject()->is_enabled ? $redirect->enable() : $redirect->disable();

            return;
        }

        $app = $event->getApp();
        $project = $event->getProject();

        // This is required because TogglesNginxConfigs::configFilename()
        // relies on the app's domain being set as configs are per-domain.
        //
        // TODO: If we switch to per-project configs, this can be removed.
        if (!$app->domain_name) {
            return;
        }

        if ($project->is_enabled) {
            $app->template()->enable();

            return;
        }

        $step = new ProgressStep('disable', 'Disabling project', 60);
        ProjectProgress::dispatch($project, $step);

        $app->template()->disable();

        ProjectProgress::dispatch($project, $step->complete());
    }
}
