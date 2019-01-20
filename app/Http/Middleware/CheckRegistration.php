<?php

namespace Servidor\Http\Middleware;

use Closure;
use Exception;

class CheckRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.registration_enabled', false) !== true) {
            abort(403, 'Registration is disabled.');
        }

        return $next($request);
    }
}
