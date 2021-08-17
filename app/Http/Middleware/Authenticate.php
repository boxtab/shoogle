<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
//    protected function redirectTo($request)
//    {
//        if (! $request->expectsJson()) {
//            return route('login');
//        }
//    }

    public function handle($request, Closure $next, ...$guards)
    {
//        if (Auth::guest()) {
//            return response()->json(['message' => 'you shall not pass']);
//        }

        return $next($request);
    }
}
