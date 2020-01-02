<?php

namespace Servidor\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Servidor\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
}
