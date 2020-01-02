<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Servidor\Http\Controllers\Controller;
use Servidor\Providers\RouteServiceProvider;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}
