<?php

namespace Servidor\Http\Controllers\Projects\Services;

use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class ViewLog extends Controller
{
    public function __invoke(Project $project, string $log, ProjectService $service): Response
    {
        $this->verifyProjectMatches($service, $project);

        return response()->make((string) $service->logs()[$log]);
    }
}
