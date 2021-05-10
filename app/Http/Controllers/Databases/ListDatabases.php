<?php

namespace Servidor\Http\Controllers\Databases;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Servidor\Database;

class ListDatabases
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function __invoke(): JsonResponse
    {
        $db = new Database();

        return new JsonResponse($db->listDatabases());
    }
}
