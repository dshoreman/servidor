<?php

namespace Servidor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Http\Requests\CreateSite;
use Servidor\Http\Requests\UpdateSite;
use Servidor\Site;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\LinuxUser;

class SiteController extends Controller
{
    public function index(): JsonResponse
    {
        /**
         * @var \Illuminate\Contracts\Support\Arrayable
         */
        $sites = Site::all();

        return response()->json($sites->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSite $request): JsonResponse
    {
        $site = Site::create($request->validated());

        return response()->json($site, Response::HTTP_CREATED);
    }

    public function showLog(Site $site, string $log): Response
    {
        return response()->make($site->readLog($log));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSite $request, Site $site): JsonResponse
    {
        $site->update($request->validated());

        if (true === $request->input('create_user')) {
            $username = Str::slug($site->name);

            try {
                SystemUser::findByName($username);
            } catch (UserNotFoundException $e) {
                $user = new LinuxUser(['name' => $username]);

                $user->setCreateHome(true);

                if ($user = SystemUser::createCustom($user)) {
                    $site->system_user = $user['uid'];
                    $site->save();
                }
            }
        }

        return response()->json($site, Response::HTTP_OK);
    }
}
