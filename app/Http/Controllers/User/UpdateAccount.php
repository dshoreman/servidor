<?php

namespace Servidor\Http\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class UpdateAccount extends Controller
{
    use RefreshDatabase;

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        \assert($user instanceof User);

        $data = $request->validate([
            'name' => ['string'],
            'email' => ['email', 'prohibits:newPassword'],
            'password' => ['current_password', 'required_with:newPassword'],
            'newPassword' => ['string', 'min:8', 'confirmed', 'required_with:password'],
            'newPassword_confirmation' => ['same:newPassword', 'required_with:newPassword'],
        ]);

        if (isset($data['newPassword'])) {
            $data['password'] = Hash::make((string) $data['newPassword']);
        }

        $user->updateOrFail($data);

        return response()->json($user);
    }
}
