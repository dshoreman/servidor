<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        exec('cat /etc/group', $lines);

        $groups = collect();

        foreach ($lines as $line) {
            list($name, $password, $id, $users) = explode(':', $line);

            $groups->push(compact('name', 'password', 'id', 'users'));
        }

        return $groups;
    }
}
