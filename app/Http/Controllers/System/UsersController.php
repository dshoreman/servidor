<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

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

        $keys = ['name', 'password', 'id', 'group_id', 'full_name', 'home_directory', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
            $users->push(array_combine($keys, explode(':', $line)));
        }

        return $users;
    }

    /**
     * Render the System Users page
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('system.users');
    }

    /**
     * Create a new user on the host system
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        if ((int) ($data['id'] ?? null) > 0) {
            $options[] = '-u '.(int) $data['id'];
        }

        $options[] = $data['name'];

        exec('sudo useradd '.implode(' ', $options), $output, $retval);

        if ($retval !== 0) {
            $data['error'] = "Something went wrong (Exit code: {$retval})";
        } else {
            $data = posix_getpwnam($data['name']);
        }

        return response($data, 0 === $retval
            ? Response::HTTP_CREATED
            : Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Update the specified user on the system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $new_id = $id;
        $data = $request->validate($this->validationRules());

        if (!$original = posix_getpwuid($id)) {
            throw $this->failed("No user found matching the given criteria.");
        }

        if ($data['name'] != $original['name']) {
            $options[] = '-l '.$data['name'];
        }

        if ($data['id'] != $id && (int) $data['id'] > 0) {
            $new_id = (int) $data['id'];
            $options[] = '-u '.$new_id;
        }

        if (empty($options ?? null)) {
            throw $this->failed("Nothing to update!");
        }

        $options[] = $original['name'];

        exec('sudo usermod '.implode(' ', $options), $output, $retval);

        if (0 !== $retval) {
            throw new ValidationException("Something went wrong. Exit code: ".$retval);
        }

        return response(posix_getpwuid($new_id), Response::HTTP_OK);
    }

    /**
     * Remove the specified user from the system.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($user = posix_getpwuid($id)) {
            exec('sudo userdel '.$user['name']);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get the validation rules for system users
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
            'id' => 'integer|nullable',
        ];
    }

    protected function failed($message, $key = 'id')
    {
        return ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
