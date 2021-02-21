<?php

namespace Servidor\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Illuminate\Http\Request $request @unused-param
     *
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return '/login';
    }
}
