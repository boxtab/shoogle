<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstant;
use App\Support\ApiResponse\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

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
            return ApiResponse::returnError(
                'The route is available only for users with the ADMIN or SUPER ADMIN role.',
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
