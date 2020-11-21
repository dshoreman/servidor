<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\CreateProject as Request;
use Servidor\Projects\Project;

class CreateProject extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validated();

        $project = Project::create([
            'name' => $data['name'],
        ]);

        $project->applications()->createMany(array_map(function (array $app): array {
            return [
                'template' => $app['template'],
                'domain_name' => $app['domain'],
                'source_provider' => $app['provider'],
                'source_repository' => $app['repository'],
                'source_branch' => $app['branch'],
            ];
        }, $data['applications'] ?? []));

        return response()->json($project->load('applications'), Response::HTTP_CREATED);
    }
}
