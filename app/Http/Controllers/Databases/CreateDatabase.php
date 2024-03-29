<?php

namespace Servidor\Http\Controllers\Databases;

use Doctrine\DBAL\Exception;
use Illuminate\Http\JsonResponse;
use Servidor\Databases\DatabaseData;
use Servidor\Databases\DatabaseManager;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\Databases\NewDatabase;

class CreateDatabase extends Controller
{
    public function __invoke(DatabaseManager $manager, NewDatabase $request): JsonResponse
    {
        try {
            return new JsonResponse($manager->create(DatabaseData::fromRequest($request)));
        } catch (Exception $_) {
            return new JsonResponse([
                'error' => 'Could not create database',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
