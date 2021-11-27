<?php

namespace Servidor\Http\Controllers\Projects\Applications;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Projects\Actions\SyncAppFiles;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class PullCode extends Controller
{
    public function __invoke(Project $project, Application $app): JsonResponse
    {
        $this->verifyProjectMatches($app, $project);

        (new SyncAppFiles($app))->execute();

        return response()->json($app, Response::HTTP_OK);
    }
}
