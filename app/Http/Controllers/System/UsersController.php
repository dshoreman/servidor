<?php

namespace Servidor\Http\Controllers\System;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\Http\Controllers\Controller;
use Servidor\System\User as SystemUser;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SystemUser::list();
    }

    /**
     * Create a new user on the host system.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        try {
            $user = SystemUser::create(
                $data['name'],
                $data['uid'] ?? null,
                $data['gid'] ?? null,
            );
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response($user, Response::HTTP_CREATED);
    }

    /**
     * Update the specified user on the system.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {
        $newUid = $uid;
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

        $original['groups'] = (new SystemUser($original))->secondaryGroups();

        if (isset($data['groups']) && $data['groups'] != $original['groups']) {
            $options[] = '-G "' . implode(',', $data['groups']) . '"';
        }

        if (empty($options ?? null)) {
            throw $this->failed('Nothing to update!');
        }

        $options[] = $original['name'];

        exec('sudo usermod ' . implode(' ', $options), $output, $retval);
        unset($output);

        if (0 !== $retval) {
            throw new Exception('Something went wrong. Exit code: ' . $retval);
        }

        $userData = posix_getpwuid($newUid);
        $userData['groups'] = (new SystemUser($userData))->secondaryGroups();

        return response($userData, Response::HTTP_OK);
    }

    /**
     * Remove the specified user from the system.
     *
     * @param int $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($uid)
    {
        if ($user = SystemUser::find($uid)) {
            $user->delete();
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
