<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Simple session-based access guard.
 *
 * The technical test only defines a single, hardcoded credential pair
 * (see AuthController), so a full Eloquent User / laravel/auth scaffold
 * would be overkill. Access is tracked with a boolean session flag instead.
 */
class CheckLogin
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
        if (! session('logged_in')) {
            return redirect()->route('login')->with('error', __('messages.please_login'));
        }

        return $next($request);
    }
}
