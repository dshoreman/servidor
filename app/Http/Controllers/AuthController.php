<?php

namespace Servidor\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\Exception;
use GuzzleHttp\Exception\BadResponseException;

class AuthController extends Controller
{
    /**
     * Proxy login requests to /oauth/token with client secret
     *
     * @param  Illuminate\Http\Request  $request
     * @param  GuzzleHttp\Client        $client
     * @return Illuminate\Http\Response
     */
    public function login (Request $request, Client $client)
    {
        try {
            $response = $client->post(config('app.url').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('passport.client_id'),
                    'client_secret' => config('passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                    'scopes' => '*',
                ],
            ]);

            return $response->getBody();
        } catch (BadResponseException $e) {
            return response(
                $e->getResponse()->getBody(),
                $e->getCode(),
            );
        }
    }
}
