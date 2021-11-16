<?php

namespace Servidor\Http\Controllers\Databases;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Servidor\Databases\DatabaseDTO;
use Servidor\Databases\DatabaseManager;

class ShowDatabase
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function __invoke(DatabaseManager $manager, string $name): JsonResponse
    {
        $database = new DatabaseDTO($name);
        $tables = $manager->tables($database);

        return new JsonResponse($database->withTables($tables));
    }
}
