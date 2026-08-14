<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request. Keeps a logged in user off the login page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session('logged_in')) {
            return redirect()->route('movies.index');
        }

        return $next($request);
    }
}
