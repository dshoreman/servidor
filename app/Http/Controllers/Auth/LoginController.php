<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class LoginController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        $auth = auth();
        assert($auth instanceof Guard);

        $user = $auth->user();
        assert($user instanceof User);

        if ($token = $user->token()) {
            $token->delete();
        }

        return response(null, 204);
    }
}
