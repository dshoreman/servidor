<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Servidor\Exceptions\System\GroupNotFoundException;
use Servidor\Exceptions\System\GroupNotModifiedException;
use Servidor\Exceptions\System\GroupSaveException;
use Servidor\Http\Controllers\Controller;
use Servidor\Http\Requests\System\CreateGroup;
use Servidor\Http\Requests\System\UpdateGroup;
use Servidor\System\Group as SystemGroup;

class GroupsController extends Controller
{
    public function index(): Response
    {
        return response(SystemGroup::list());
    }

    public function store(CreateGroup $request): Response
    {
        $data = $request->validated();

        try {
            $group = SystemGroup::create(
                $data['name'],
                $data['system'] ?? false,
                $data['gid'] ?? null,
            );
        } catch (GroupSaveException $e) {
            $data['error'] = $e->getMessage();

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response($group, Response::HTTP_CREATED);
    }

    public function update(UpdateGroup $request, int $gid): Response
    {
        try {
            $group = SystemGroup::find($gid);

            return response(
                $group->update($request->validated()),
                Response::HTTP_OK
            );
        } catch (GroupNotFoundException $e) {
            throw $this->fail('No group found matching the given criteria.');
        } catch (GroupNotModifiedException $e) {
            throw $this->fail('Nothing to update!');
        } catch (GroupSaveException $e) {
            throw $this->fail($e->getMessage());
        }
    }

    public function destroy(int $gid): Response
    {
        SystemGroup::find($gid)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function fail(string $message, string $key = 'gid'): ValidationException
    {
        return ValidationException::withMessages([$key => $message]);
    }
}
