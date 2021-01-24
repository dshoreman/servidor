<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\Response;
use Servidor\Projects\Project;

class RemoveProject extends Controller
{
    /** @return \Illuminate\Contracts\Routing\ResponseFactory|Response */
    public function __invoke(Project $project)
    {
        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
