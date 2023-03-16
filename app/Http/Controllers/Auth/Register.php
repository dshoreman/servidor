<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class Register extends Controller
{
    /** @var array<string, mixed> */
    private array $validationRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('register');
    }

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate($this->validationRules);

        $user = new User();
        $user->name = (string) $request->name;
        $user->email = (string) $request->email;
        $user->password = Hash::make((string) $request->password);
        $user->save();

        return new JsonResponse($user, JsonResponse::HTTP_CREATED);
    }
}
