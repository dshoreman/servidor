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
        $data = $request->validated();

        $app = new Application([
            'template' => $data['template'] ?? '',
            'domain_name' => $data['domain'] ?? '',
            'source_provider' => $data['provider'] ?? '',
            'source_repository' => $data['repository'] ?? '',
            'source_branch' => $data['branch'] ?? '',
        ]);

        $project->applications()->save($app);

        return response()->json($app, Response::HTTP_CREATED);
    }
}
