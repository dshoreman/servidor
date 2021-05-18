<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Servidor\Http\Controllers\Controller;

class Logout extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $guard = Auth::guard('web');
        \assert($guard instanceof StatefulGuard);

        $guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
