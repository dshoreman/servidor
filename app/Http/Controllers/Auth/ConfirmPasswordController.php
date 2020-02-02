<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Servidor\Http\Controllers\Controller;
use Servidor\Providers\RouteServiceProvider;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
