<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Servidor\Http\Controllers\Controller;
use Servidor\Projects\Project;

class ListProjects extends Controller
{
    public function __invoke(): JsonResponse
    {
        $projects = Project::with('applications')->get();

        return response()->json($projects);
    }
}
