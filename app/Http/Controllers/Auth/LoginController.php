<?php

namespace Servidor\Http\Controllers\Auth;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Servidor\Http\Controllers\Controller;

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
            $response = $client->post(config('app.url') . '/oauth/token', [
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

            return response(
                $response ? (string) $response->getBody() : '',
                (int) $e->getCode(),
            );
        }
    }

    protected function username()
    {
        return 'username';
    }

    public function logout()
    {
        /** @var \Illuminate\Contracts\Auth\Guard */
        $auth = auth();
        /** @var \Servidor\User */
        $user = $auth->user();
        $token = $user->token();

        if ($token && true !== $token->delete()) {
            throw new Exception('Failed to delete token.');
        }

        return response(null, 204);
    }
}
