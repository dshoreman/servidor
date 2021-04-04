<?php

namespace Servidor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Servidor\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
    /**
     * Redirect authed users to home for guest-only routes.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
