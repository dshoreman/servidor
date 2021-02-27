<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Servidor\Http\Controllers\Controller;
use Servidor\Providers\RouteServiceProvider;
use Servidor\User;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected string $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('register');
    }

    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data): User
    {
        $user = new User();
        $user->name = (string) $data['name'];
        $user->email = (string) $data['email'];
        $user->password = Hash::make((string) $data['password']);

        $user->save();

        return $user;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Request $request @unused-param
     */
    protected function registered(Request $request, User $user): JsonResponse
    {
        return response()->json($user);
    }
}
