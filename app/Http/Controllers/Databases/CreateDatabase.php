<?php

namespace Servidor\Http\Controllers\Databases;

use Exception;
use Illuminate\Http\JsonResponse;
use Servidor\Databases\Database;
use Servidor\Databases\DatabaseManager;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\Databases\NewDatabase;

class CreateDatabase extends Controller
{
    public function __invoke(DatabaseManager $manager, NewDatabase $request): JsonResponse
    {
        try {
            $database = Database::fromRequest($request);

            $manager->create($database);

            return new JsonResponse($database);
        } catch (Exception $_) {
            return new JsonResponse([
                'error' => 'Could not create database',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
