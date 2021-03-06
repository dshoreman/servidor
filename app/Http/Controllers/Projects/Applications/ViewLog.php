<?php

namespace Servidor\Http\Controllers\Projects\Applications;

use Illuminate\Http\Response;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class ViewLog extends Controller
{
    public function __invoke(Project $project, string $log, Application $app): Response
    {
        $this->verifyProjectMatches($app, $project);

        return response()->make((string) $app->logs()[$log]);
    }
}
