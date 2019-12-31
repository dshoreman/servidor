<?php

namespace Servidor\Http\Middleware;

use Closure;

class CheckRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (true !== config('app.registration_enabled', false)) {
            abort(403, 'Registration is disabled.');
        }

        return $next($request);
    }
}
