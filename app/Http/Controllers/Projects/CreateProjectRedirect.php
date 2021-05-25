<?php

namespace Servidor\Http\Controllers\Projects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\Projects\NewProjectRedirect;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class CreateProjectRedirect extends Controller
{
    public function __invoke(NewProjectRedirect $request, Project $project): JsonResponse
    {
        $redirect = new Redirect($request->validated());

        $project->redirects()->save($redirect);

        return response()->json($redirect, Response::HTTP_CREATED);
    }
}
