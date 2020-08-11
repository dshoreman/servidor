<?php

namespace Servidor\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Database;

class DatabaseController extends Controller
{
    /**
     * Display a list of databases.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function index()
    {
        $db = new Database();

        return response($db->listDatabases());
    }

    /**
     * Create a new database.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'database' => 'required|string',
        ]);

        try {
            (new Database())->create($data['database']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create database'], 500);
        }

        return response()->json($data['database']);
    }
}
