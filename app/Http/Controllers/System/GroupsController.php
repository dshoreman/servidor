<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Servidor\Http\Requests\System\CreateGroup;
use Servidor\Http\Requests\System\UpdateGroup;
use Servidor\System\Group as SystemGroup;
use Servidor\System\Groups\GenericGroupSaveFailure;
use Servidor\System\Groups\GroupNotFound;
use Servidor\System\Groups\GroupNotModified;

class GroupsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SystemGroup::list());
    }

    public function store(CreateGroup $request): JsonResponse
    {
        $data = (array) $request->validated();

        try {
            $group = SystemGroup::create(
                (string) $data['name'],
                (bool) ($data['system'] ?? false),
                isset($data['gid']) ? (int) $data['gid'] : null,
            );
        } catch (GenericGroupSaveFailure $e) {
            $data['error'] = $e->getMessage();

            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($group, Response::HTTP_CREATED);
    }

    public function update(UpdateGroup $request, int $gid): JsonResponse
    {
        try {
            $group = SystemGroup::find($gid);

            return response()->json(
                $group->update((array) $request->validated()),
                Response::HTTP_OK,
            );
        } catch (GroupNotFound $_) {
            throw $this->fail('gid', 'No group found matching the given criteria.');
        } catch (GroupNotModified $_) {
            throw $this->fail('gid', 'Nothing to update!');
        } catch (GenericGroupSaveFailure $e) {
            throw $this->fail('gid', $e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy(int $gid)
    {
        SystemGroup::find($gid)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
