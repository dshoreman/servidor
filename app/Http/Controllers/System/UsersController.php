<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\System\SaveUser;
use Servidor\System\User as SystemUser;

class UsersController extends Controller
{
    public function index(): Response
    {
        return response(SystemUser::list());
    }

    public function store(SaveUser $request): Response
    {
        $data = $request->validated();

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

    public function update(SaveUser $request, int $uid): Response
    {
        try {
            $user = SystemUser::find($uid)->update(
                $request->validated(),
            );

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

    public function destroy(int $uid): Response
    {
        if ($user = SystemUser::find($uid)) {
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function fail(string $message, string $key = 'uid'): void
    {
        throw ValidationException::withMessages([$key => $message]);
    }
}
