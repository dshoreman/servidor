<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Exceptions\System\UserNotModifiedException;
use Servidor\Exceptions\System\UserSaveException;
use Servidor\Http\Requests\System\CreateUser;
use Servidor\Http\Requests\System\UpdateUser;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class UsersController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SystemUser::list());
    }

    public function store(CreateUser $request): JsonResponse
    {
        $data = $request->validated();
        $createGroup = (bool) $request->input('user_group', false);

        try {
            $user = new LinuxUser(['name' => $data['name']]);

            $user->setCreateHome((bool) $request->input('create_home', false))
                ->setHomeDirectory((string) ($data['dir'] ?? ''))
                ->setShell((string) ($data['shell'] ?? ''))
                ->setSystem((bool) ($data['system'] ?? false))
                ->setUid(isset($data['uid']) ? (int) $data['uid'] : null);

            $gid = isset($data['gid']) ? (int) $data['gid'] : null;
            if (!$createGroup && $gid) {
                $user->setGid($gid);
            }

            $user->setUserGroup($createGroup);

            $user = SystemUser::createCustom($user);
        } catch (UserSaveException $e) {
            $data['error'] = $e->getMessage();

            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(UpdateUser $request, int $uid): JsonResponse
    {
        try {
            $user = SystemUser::find($uid);

            return response()->json(
                $user->update($request->validated()),
                Response::HTTP_OK
            );
        } catch (UserNotFoundException $e) {
            throw $this->fail('uid', 'No user found matching the given criteria.');
        } catch (UserNotModifiedException $e) {
            throw $this->fail('uid', 'Nothing to update!');
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy(Request $request, int $uid)
    {
        $withHome = (bool) $request->input('deleteHome', false);

        SystemUser::find($uid)->delete($withHome);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
