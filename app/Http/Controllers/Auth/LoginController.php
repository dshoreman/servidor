<?php

namespace Servidor\Http\Controllers\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Servidor\Http\Controllers\Controller;
use Servidor\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Proxy login requests to /oauth/token with client secret.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request, Client $client)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        try {
            $appUrl = (string) Config::get('app.url');

            $response = $client->post($appUrl . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('passport.client_id'),
                    'client_secret' => config('passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                    'scopes' => '*',
                ],
            ]);

            $this->clearLoginAttempts($request);

            return response((string) $response->getBody());
        } catch (BadResponseException $e) {
            $this->incrementLoginAttempts($request);
            $response = $e->getResponse();

            return response((string) $response->getBody(), (int) $e->getCode());
        }
    }

    protected function username(): string
    {
        return 'username';
    }

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
