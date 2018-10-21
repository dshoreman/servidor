<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['username', 'password', 'id', 'group_id', 'full_name', 'home_directory', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
            $users->push(array_combine($keys, explode(':', $line)));
        }

        return $users;
    }
}
