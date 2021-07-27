<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ( Auth::user()->roles()->first()->name !== RoleConstant::COMPANY_ADMIN ) {
            return response()->json([
                'success' => false,
                'data' => ['message' => 'The route is available only for users with the ADMIN role.'],
            ]);
        }

        return $next($request);
    }
}
