<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\Projects\NewProjectService;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class CreateProjectService extends Controller
{
    public function __invoke(NewProjectService $request, Project $project): JsonResponse
    {
        $service = new ProjectService($request->validated());

        $project->services()->save($service);

        return response()->json($service, Response::HTTP_CREATED);
    }
}
