<?php

namespace Servidor\Projects;

use Servidor\Projects\Applications\ProjectAppSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaved;

class ToggleProjectVisibility
{
    public function handle(ProjectAppSaved|ProjectRedirectSaved $event): void
    {
        $project = $event->getProject();
        $step = $this->step($event, $project);

        if ($event instanceof ProjectAppSaved) {
            $this->toggleApplication($step, $project, $event->getApp());
        }

        if ($event instanceof ProjectRedirectSaved) {
            $redirect = $event->getRedirect();
            $project->is_enabled ? $redirect->enable() : $redirect->disable();
        }

        ProjectProgress::dispatch($project, $step->complete());
    }

    private function toggleApplication(ProgressStep $step, Project $project, Application $app): void
    {
        // This is required because TogglesNginxConfigs::configFilename()
        // relies on the app's domain being set as configs are per-domain.
        //
        // TODO: If we switch to per-project configs, this can be removed.
        if (!$app->domain_name) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));

            return;
        }

        $project->is_enabled ? $app->template()->enable() : $app->template()->disable();
    }

    private function step(ProjectAppSaved|ProjectRedirectSaved $event, Project $project): ProgressStep
    {
        $type = $event instanceof ProjectAppSaved ? 'project' : 'redirect';
        $text = ($project->is_enabled ? 'Enabling ' : 'Disabling ') . $type;

        $step = new ProgressStep($project->is_enabled ? 'enable' : 'disable', $text, 60);
        ProjectProgress::dispatch($project, $step);

        return $step;
    }
}
