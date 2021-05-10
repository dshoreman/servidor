<?php

namespace Servidor\Http\Controllers\Databases;

use Exception;
use Illuminate\Http\JsonResponse;
use Servidor\Database;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\Databases\NewDatabase;

class CreateDatabase extends Controller
{
    public function __invoke(NewDatabase $request): JsonResponse
    {
        $database = $request->validated();

        try {
            $created = (new Database())->create($database['name']);
        } catch (Exception $_) {
            $created = false;
        }

        return $created
            ? new JsonResponse($database)
            : new JsonResponse(
                ['error' => 'Could not create database'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
    }
}
