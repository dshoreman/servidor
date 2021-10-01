<?php

namespace Servidor\Projects;

use Servidor\Projects\Applications\ProjectAppSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaved;

class ToggleProjectVisibility
{
    public function handle(ProjectAppSaved|ProjectRedirectSaved $event): void
    {
        $project = $event->getProject();
        $appOrRedirect = $event->getAppOrRedirect();
        $step = $this->addStep($project, $appOrRedirect);

        // This is required because TogglesNginxConfigs::configFilename()
        // relies on the app's domain being set as configs are per-domain.
        //
        // TODO: If we switch to per-project configs, this can be removed.
        if (!$appOrRedirect->domain_name) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));

            return;
        }

        $project->is_enabled ? $appOrRedirect->enable() : $appOrRedirect->disable();

        ProjectProgress::dispatch($project, $step->complete());
    }

    private function addStep(
        Project $project,
        Application|Redirect $appOrRedirect,
    ): ProgressStep {
        $text = ($project->is_enabled ? 'Enabling ' : 'Disabling ') . $appOrRedirect->getType();

        $step = new ProgressStep($project->is_enabled ? 'enable' : 'disable', $text, 60);
        ProjectProgress::dispatch($project, $step);

        return $step;
    }
}
