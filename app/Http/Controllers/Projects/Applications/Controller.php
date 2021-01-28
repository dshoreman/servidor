<?php

namespace Servidor\Http\Controllers\Projects\Applications;

use Exception;
use Servidor\Http\Controllers\Projects\Controller as BaseController;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class Controller extends BaseController
{
    protected function verifyProjectMatches(Application $app, Project $project): void
    {
        /** @var Project */
        $relatedProject = $app->project;

        if ($project->id === $relatedProject->id) {
            return;
        }

        throw new Exception('Project mismatch');
    }
}
