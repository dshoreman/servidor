<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Routing\Controller;
use Servidor\Providers\RouteServiceProvider;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only(['verify', 'resend']);
    }
}
