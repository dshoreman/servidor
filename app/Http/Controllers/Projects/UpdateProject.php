<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Servidor\Http\Requests\UpdateProjectRequest;
use Servidor\Projects\Project;

class UpdateProject extends Controller
{
    public function __invoke(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update((array) $request->validated());
        $project->load(['services']);

        return response()->json($project);
    }
}
