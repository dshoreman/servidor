<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\CreateProjectRequest;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class CreateProject extends Controller
{
    public function __invoke(CreateProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        $project = Project::create([
            'name' => $data['name'],
            'is_enabled' => $data['is_enabled'] ?? false,
        ]);

        $project->applications()->saveMany(array_map(function (array $app): Application {
            return new Application([
                'template' => $app['template'] ?? '',
                'domain_name' => $app['domain'] ?? '',
                'source_provider' => $app['provider'] ?? '',
                'source_repository' => $app['repository'] ?? '',
                'source_branch' => $app['branch'] ?? '',
            ]);
        }, (array) ($data['applications'] ?? [])));

        $project->redirects()->saveMany(array_map(function (array $redirect): Redirect {
            return new Redirect([
                'domain_name' => $redirect['domain'],
                'target' => $redirect['target'],
                'type' => $redirect['type'],
            ]);
        }, (array) ($data['redirects'] ?? [])));

        return response()->json(
            $project->load(['applications', 'redirects']),
            Response::HTTP_CREATED
        );
    }
}
