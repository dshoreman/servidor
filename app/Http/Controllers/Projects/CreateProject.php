<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\CreateProjectRequest;
use Servidor\Projects\Project;

class CreateProject extends Controller
{
    public function __invoke(CreateProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        $project = new Project([
            'name' => $data['name'],
            'is_enabled' => $data['is_enabled'] ?? false,
        ]);
        $project->save();

        return response()->json(
            $project->load(['applications', 'redirects']),
            Response::HTTP_CREATED
        );
    }
}
