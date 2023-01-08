<?php

namespace Servidor\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Servidor\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
    /**
     * Redirect authed users to home for guest-only routes.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string ...$guards)
    {
        if ($this->isAuthed($guards ?: [null])) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }

    /**
     * Check if any of the given guards are authed.
     *
     * @param array<?string> $guards
     */
    private function isAuthed(array $guards): bool
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return true;
            }
        }

        return false;
    }
}
