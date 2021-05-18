<?php

namespace Servidor\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class ShowProfile extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        \assert($user instanceof User);

        return response()->json($user->toArray());
    }
}
