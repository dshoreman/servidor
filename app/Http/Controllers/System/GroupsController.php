<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

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
        $data = $request->validate($this->validationRules());

        exec('sudo groupadd '.$data['name'], $output, $retval);

        if ($data['users'] ?? null === null) {
            $data['users'] = '';
        }

        switch ($retval) {
            case 0:
                $group = posix_getgrnam($data['name']);

                $data = [
                    'id' => $group['gid'],
                    'name' => $group['name'],
                    'users' => $group['members'],
                ];

                break;
            case 2: $data['error'] = 'Invalid command syntax.'; break;
            case 3: $data['error'] = 'Invalid argument to option'; break;
            case 4: $data['error'] = 'GID not unique (when -o not used)'; break;
            case 9: $data['error'] = 'Group name not unique'; break;
            case 10: $data['error'] = "Can't update group file"; break;
        }

        return response($data, 0 === $retval
            ? Response::HTTP_CREATED
            : Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Update the specified group on the system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate($this->validationRules());

        if (!$original = posix_getgrgid($id)) {
            throw $this->failed("No group found matching the given criteria.");
        }

        if ($data['name'] != $original['name']) {
            $options[] = '-n '.$data['name'];
        }

        if (empty($options ?? null)) {
            throw $this->failed("Nothing to update!");
        }

        $options[] = $original['name'];

        exec('sudo groupmod '.implode(' ', $options), $output, $retval);

        if (0 !== $retval) {
            throw new ValidationException("Something went wrong. Exit code: ".$retval);
        }

        $updated = posix_getgrgid($id);

        return response([
            'id' => $updated['gid'],
            'name' => $updated['name'],
            'users' => $updated['members'],
        ], Response::HTTP_OK);
    }

    /**
     * Get the validation rules for system groups
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
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
        ];
    }

    protected function failed($message, $key = 'id')
    {
        return ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
