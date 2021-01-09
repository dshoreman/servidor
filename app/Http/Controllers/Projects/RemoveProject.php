<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\Response;
use Servidor\Http\Controllers\Controller;
use Servidor\Projects\Project;

class RemoveProject extends Controller
{
    public function __invoke(Project $project): Response
    {
        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
