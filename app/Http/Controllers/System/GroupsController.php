<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Http\Controllers\Controller;
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

        $keys = ['name', 'password', 'gid', 'users'];
        $groups = collect();

        foreach ($lines as $line) {
            $group = array_combine($keys, explode(':', $line));
            $group['users'] = '' == $group['users'] ? [] : explode(',', $group['users']);

            $groups->push($group);
        }

        return $groups;
    }

    /**
     * Create a new group on the host system.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        if ((int) ($data['gid'] ?? null) > 0) {
            $options[] = '-g ' . (int) $data['gid'];
        }

        $options[] = $data['name'];

        exec('sudo groupadd ' . implode(' ', $options), $output, $retval);

        if ($data['users'] ?? null === null) {
            $data['users'] = '';
        }

        switch ($retval) {
            case 0:
                $group = posix_getgrnam($data['name']);

                $data = [
                    'gid' => $group['gid'],
                    'name' => $group['name'],
                    'users' => $group['members'],
                ];

                break;
            case 2:
                $data['error'] = 'Invalid command syntax.';
                break;
            case 3:
                $data['error'] = 'Invalid argument to option';
                break;
            case 4:
                $data['error'] = 'GID not unique (when -o not used)';
                break;
            case 9:
                $data['error'] = 'Group name not unique';
                break;
            case 10:
                $data['error'] = "Can't update group file";
                break;
        }

        return response($data, 0 === $retval
            ? Response::HTTP_CREATED
            : Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Update the specified group on the system.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $gid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gid)
    {
        $data = $request->validate($this->validationRules());
        $data['gid'] = (int) ($data['gid'] ?? $gid);

        if (!$original = posix_getgrgid($gid)) {
            throw $this->failed('No group found matching the given criteria.');
        }
        $updated = $original;

        if ($data['name'] != $original['name']) {
            $options[] = '-n ' . $data['name'];
        }

        if ($data['gid'] != $gid && $data['gid'] > 0) {
            $options[] = '-g ' . $data['gid'];
        }

        if (($data['users'] ?? []) != $original['members']) {
            $members = implode(',', $data['users']);
        }

        if (empty($options ?? null) && !isset($members)) {
            throw $this->failed('Nothing to update!');
        }

        if (count($options ?? [])) {
            $options[] = $original['name'];

            exec('sudo groupmod ' . implode(' ', $options), $output, $retval);

            if (0 !== $retval) {
                throw new ValidationException('Something went wrong. Exit code: ' . $retval);
            }

            $updated = posix_getgrgid($data['gid']);
        }

        if ($members ?? null) {
            $group = $updated['name'];

            exec("sudo gpasswd -M '{$members}' {$group}", $output, $retval);

            if (0 !== $retval) {
                throw new ValidationException('Something went wrong. Exit code: ' . $retval);
            }

            $updated = posix_getgrgid($data['gid']);
        }

        return response([
            'gid' => $updated['gid'],
            'name' => $updated['name'],
            'users' => $updated['members'],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified group from the system.
     *
     * @param int $gid
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($gid)
    {
        if ($group = posix_getgrgid($gid)) {
            exec('sudo groupdel ' . $group['name']);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get the validation rules for system groups.
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
            'gid' => 'integer|nullable',
            'groups' => 'array|nullable',
            'users' => 'array|nullable',
        ];
    }

    protected function failed($message, $key = 'gid')
    {
        return ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
