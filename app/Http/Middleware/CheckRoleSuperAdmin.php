<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Constants\RoleConstant;

class CheckRoleSuperAdmin
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
        if ( Auth::user()->roles()->first()->name !== RoleConstant::SUPER_ADMIN ) {
            return response()->json([
                'success' => false,
                'data' => ['message' => 'The route is available only for users with the super admin role.'],
            ]);
        }

        return $next($request);
    }
}
