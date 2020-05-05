<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\System\CreateUser;
use Servidor\Http\Requests\System\UpdateUser;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class UsersController extends Controller
{
    public function index(): Response
    {
        return response(SystemUser::list());
    }

    public function store(CreateUser $request): Response
    {
        $data = $request->validated();
        $createGroup = $request->input('user_group', false);

        try {
            $user = new LinuxUser(['name' => $data['name']]);

            $user->setCreateHome($request->input('create_home', false))
                        ->setHomeDirectory($data['dir'] ?? '')
                        ->setShell($data['shell'] ?? null)
                        ->setSystem($data['system'] ?? false)
                        ->setUid($data['uid'] ?? null);

            if (!$createGroup && $data['gid'] ?? null) {
                $user->setGid($data['gid']);
            }

            $user->setUserGroup($createGroup);

            $user = SystemUser::createCustom($user);
        } catch (UserSaveException $e) {
            $data['error'] = $e->getMessage();

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response($user, Response::HTTP_CREATED);
    }

    public function update(UpdateUser $request, int $uid): Response
    {
        try {
            $user = SystemUser::find($uid);

            return response(
                $user->update($request->validated()),
                Response::HTTP_OK
            );
        } catch (UserNotFoundException $e) {
            throw $this->fail('No user found matching the given criteria.');
        } catch (UserNotModifiedException $e) {
            throw $this->fail('Nothing to update!');
        }
    }

    public function destroy(Request $request, int $uid): Response
    {
        $withHome = (bool) $request->input('deleteHome', false);

        SystemUser::find($uid)->delete($withHome);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function fail(string $message, string $key = 'uid'): ValidationException
    {
        return ValidationException::withMessages([$key => $message]);
    }
}
