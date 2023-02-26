<?php

namespace Servidor\Projects;

use Servidor\Projects\Actions\EnableOrDisableProject;
use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Services\ProjectServiceSaved;

class ToggleProjectVisibility
{
    public function handle(ProjectSaved|ProjectServiceSaved $event): void
    {
        $project = $event->getProject();
        $service = $event->getService();

        if (null === $service) {
            return;
        }
        $step = $this->addStep($project, $service);

        try {
            (new EnableOrDisableProject($service))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (MissingProjectData $_) {
            ProjectProgress::dispatch($project, $step->skip(ProgressStep::REASON_MISSING_DATA));
        }
    }

    private function addStep(
        Project $project,
        ProjectService $service,
    ): ProgressStep {
        $type = $service->getType();

        [$step, $text, $progress] = $project->is_enabled
            ? ['enable', 'Enabling ' . $type, 90]
            : ['disable', 'Disabling ' . $type, 70];

        $step = new ProgressStep($step, $text, $progress);
        ProjectProgress::dispatch($project, $step->start());

        return $step;
    }
}
