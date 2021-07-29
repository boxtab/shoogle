<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRoleAdminAndSuperAdmin
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
        $roleName = Auth::user()->roles()->first()->name;
        $lisAllowedRoles = [
            RoleConstant::SUPER_ADMIN,
            RoleConstant::COMPANY_ADMIN,
        ];

        if ( ! in_array( $roleName, $lisAllowedRoles ) ) {
            return response()->json([
                'success' => false,
                'data' => ['message' => 'The route is available only for users with the ADMIN or SUPER ADMIN role.'],
            ]);
        }

        return $next($request);
    }
}