<?php

namespace Servidor\Http\Controllers\Projects\Services;

use Exception;
use Servidor\Http\Controllers\Projects\Controller as BaseController;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class Controller extends BaseController
{
    protected function verifyProjectMatches(ProjectService $service, Project $project): void
    {
        /** @var Project $relatedProject */
        $relatedProject = $service->project;

        if ($project->id === $relatedProject->id) {
            return;
        }

        throw new Exception('Project mismatch');
    }
}
