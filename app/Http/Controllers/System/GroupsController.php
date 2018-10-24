<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * Render the System Groups page
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('system.groups');
    }

    /**
     * Create a new group on the host system
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required', 'max:32', 'bail',
                function ($attribute, $value, $fail) {
                    if (str_contains($value, ':')) {
                        $fail("The {$attribute} cannot contain a colon.");
                    }

                    if (str_contains($value, ',')) {
                        $fail("The {$attribute} cannot contain a comma.");
                    }

                    if (str_contains($value, ["\t", "\n", ' '])) {
                        $fail("The {$attribute} cannot contain whitespace or newlines.");
                    }
                },
                'regex:/^[a-z_][a-z0-9_-]*[\$]?$/',
            ],
            'users' => 'string|nullable',
        ]);

        exec('sudo groupadd '.$data['name'], $output, $retval);

        if ($data['users'] ?? null === null) {
            $data['users'] = '';
        }

        switch ($retval) {
            case 0:
                return response($data, Response::HTTP_CREATED);
            case 2: $data['error'] = 'Invalid command syntax.'; break;
            case 3: $data['error'] = 'Invalid argument to option'; break;
            case 4: $data['error'] = 'GID not unique (when -o not used)'; break;
            case 9: $data['error'] = 'Group name not unique'; break;
            case 10: $data['error'] = "Can't update group file"; break;
        }

        $data['exit_code'] = $retval;

        return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
