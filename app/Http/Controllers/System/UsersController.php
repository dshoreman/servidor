<?php

namespace Servidor\Http\Controllers\System;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        exec('cat /etc/passwd', $lines);

        $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
        $users = collect();

        foreach ($lines as $line) {
            $user = array_combine($keys, explode(':', $line));
            $user['groups'] = $this->loadSecondaryGroups($user);

            $users->push($user);
        }

        return $users;
    }

    protected function loadSecondaryGroups(array $user)
    {
        $groups = [];
        $primary = explode(':', exec('getent group ' . $user['gid']));
        $effective = explode(' ', exec('groups ' . $user['name']));

        $primaryName = reset($primary);
        $primaryMembers = explode(',', end($primary));

        foreach ($effective as $group) {
            if ($group == $primaryName && !in_array($group, $primaryMembers)) {
                continue;
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Create a new user on the host system.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());
        $options = [];

        if ((int) ($data['uid'] ?? null) > 0) {
            $options[] = '-u ' . (int) $data['uid'];
        }

        if ((int) ($data['gid'] ?? null) > 0) {
            $options[] = '-g ' . (int) $data['gid'];
        }

        $options[] = $data['name'];

        exec('sudo useradd ' . implode(' ', $options), $output, $retval);
        unset($output);

        if (0 !== $retval) {
            $data['error'] = "Something went wrong (Exit code: {$retval})";

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response(posix_getpwnam($data['name']), Response::HTTP_CREATED);
    }

    /**
     * Update the specified user on the system.
     */
    public function update(Request $request, int $uid)
    {
        $newUid = $uid;
        $options = [];
        $data = $request->validate($this->validationRules());

        if (!$original = posix_getpwuid($uid)) {
            throw $this->failed('No user found matching the given criteria.');
        }

        if ($data['name'] != $original['name']) {
            $options[] = '-l ' . $data['name'];
        }

        if (isset($data['uid']) && $data['uid'] != $uid && (int) $data['uid'] > 0) {
            $newUid = (int) $data['uid'];
            $options[] = '-u ' . $newUid;
        }

        if ($data['gid'] != $original['gid'] && (int) $data['gid'] > 0) {
            $options[] = '-g ' . (int) $data['gid'];
        }

        $original['groups'] = $this->loadSecondaryGroups($original);

        if (isset($data['groups']) && $data['groups'] != $original['groups']) {
            $options[] = '-G "' . implode(',', $data['groups']) . '"';
        }

        if (empty($options)) {
            throw $this->failed('Nothing to update!');
        }

        $options[] = $original['name'];

        exec('sudo usermod ' . implode(' ', $options), $output, $retval);
        unset($output);

        if (0 !== $retval) {
            throw new Exception('Something went wrong. Exit code: ' . $retval);
        }

        return response(posix_getpwuid($newUid), Response::HTTP_OK);
    }

    /**
     * Remove the specified user from the system.
     *
     * @param int $uid
     */
    public function destroy($uid)
    {
        if ($user = posix_getpwuid($uid)) {
            exec('sudo userdel ' . $user['name']);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get the validation rules for system users.
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
            'uid' => 'integer|nullable',
            'gid' => 'integer|required',
            'groups' => 'array|nullable',
        ];
    }

    protected function failed($message, $key = 'uid')
    {
        return ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
