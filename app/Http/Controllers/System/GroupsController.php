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

        $keys = ['name', 'password', 'id', 'users'];
        $groups = collect();

        foreach ($lines as $line) {
            $groups->push(array_combine($keys, explode(':', $line)));
        }

        return $groups;
    }

    /**
     * Render the groups list page
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('system.groups');
    }
}
