<?php

namespace Servidor\Http\Controllers;

use Servidor\Database;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $db = new Database();

        return $db->listDatabases();
    }
}
