<?php

namespace Servidor\Http\Controllers\Databases;

use Exception;
use Illuminate\Http\JsonResponse;
use Servidor\Database;
use Servidor\Http\Controllers\Controller;

class CreateDatabase extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'database' => 'required|string',
        ]);

        $db = new Database();
        $dbname = (string) $data['database'];

        try {
            $created = $db->create($dbname);
        } catch (Exception $_) {
            $created = false;
        }

        return $created
            ? new JsonResponse(['name' => $dbname])
            : new JsonResponse(['error' => 'Could not create database'], 500);
    }
}
