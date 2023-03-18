<?php

namespace Servidor\Http\Controllers\Databases;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Servidor\Databases\DatabaseManager;

class ListDatabases
{
    use AuthorizesRequests;

    public function __invoke(DatabaseManager $manager): JsonResponse
    {
        return new JsonResponse($manager->detailedDatabases());
    }
}
