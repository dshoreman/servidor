<?php

namespace Servidor\Http\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class UpdateAccount extends Controller
{
    use RefreshDatabase;

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        \assert($user instanceof User);

        $user->updateOrFail((array) $request->input());

        return response()->json($user);
    }
}
