<?php

namespace Servidor\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Database;

class DatabaseController extends Controller
{
    /**
     * Display a list of databases.
     *
     * @return Response
     */
    public function index()
    {
        $db = new Database();

        return $db->listDatabases();
    }

    /**
     * Create a new database.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'database' => 'required|string',
        ]);

        $created = (new Database())->create($data['database']);

        if (!$created) {
            throw new Exception('Could not create datbase');
        }

        return response($data['database'], Response::HTTP_OK);
    }
}
