<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\Projects\NewProjectApp;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class CreateProjectApp extends Controller
{
    public function __invoke(NewProjectApp $request, Project $project): JsonResponse
    {
        $app = new Application($request->validated());

        $project->applications()->save($app);

        return response()->json($app, Response::HTTP_CREATED);
    }
}
