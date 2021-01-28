<?php

namespace Servidor\Http\Controllers\System;

use Illuminate\Validation\ValidationException;
use Servidor\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    protected function fail(string $key, string $message): ValidationException
    {
        return ValidationException::withMessages([$key => $message]);
    }
}
