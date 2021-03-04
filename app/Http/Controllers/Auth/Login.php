<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Servidor\Http\Controllers\Controller;

class Login extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
        }

        throw ValidationException::withMessages(['email' => [trans('auth.failed')]]);
    }
}
