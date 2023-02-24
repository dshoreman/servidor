<?php

namespace Servidor\Projects;

use Servidor\Projects\Actions\EnableOrDisableProject;
use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Redirects\ProjectRedirectSaved;
use Servidor\Projects\Services\ProjectServiceSaved;

class ToggleProjectVisibility
{
    public function handle(ProjectSaved|ProjectServiceSaved|ProjectRedirectSaved $event): void
    {
        $project = $event->getProject();
        $appOrRedirect = $event->getAppOrRedirect();

        if (null === $appOrRedirect) {
            return;
        }
        $step = $this->addStep($project, $appOrRedirect);

        try {
            (new EnableOrDisableProject($appOrRedirect))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (MissingProjectData $_) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));
        }
    }

    private function addStep(
        Project $project,
        ProjectService|Redirect $appOrRedirect,
    ): ProgressStep {
        $type = $appOrRedirect->getType();

        [$step, $text, $progress] = $project->is_enabled
            ? ['enable', 'Enabling ' . $type, 90]
            : ['disable', 'Disabling ' . $type, 70];

        $step = new ProgressStep($step, $text, $progress);
        ProjectProgress::dispatch($project, $step->start());

        return $step;
    }
}
