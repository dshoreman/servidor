<?php

namespace Servidor\Http\Controllers\Projects\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Projects\Actions\SyncAppFiles;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class PullCode extends Controller
{
    public function __invoke(Project $project, ProjectService $service): JsonResponse
    {
        $this->verifyProjectMatches($service, $project);

        (new SyncAppFiles($service))->execute();

        return response()->json($service, Response::HTTP_OK);
    }
}
