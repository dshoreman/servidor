<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;
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
        } catch (UserSaveException $e) {
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
    public function update(Request $request, $uid): Response
    {
        try {
            $data = $request->validate($this->validationRules());

            $user = SystemUser::find($uid)->update($data);

            return response(
                $user->toArray(),
                Response::HTTP_OK,
            );
        } catch (UserNotFoundException $e) {
            $this->fail('No user found matching the given criteria.');
        } catch (UserNotModifiedException $e) {
            $this->fail('Nothing to update!');
        }
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

    protected function fail($message, $key = 'uid')
    {
        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
