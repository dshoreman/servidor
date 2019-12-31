<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Servidor\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
