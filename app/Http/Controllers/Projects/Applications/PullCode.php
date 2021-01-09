<?php

namespace Servidor\Http\Controllers\Projects\Applications;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Http\Controllers\Controller;
use Servidor\Projects\Application;
use Servidor\Projects\Project;

class PullCode extends Controller
{
    public function __invoke(Request $request, Project $project, Application $app): JsonResponse
    {
        $app->template()->pullCode();

        return response()->json($app, Response::HTTP_OK);
    }
}
