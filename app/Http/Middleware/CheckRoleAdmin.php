<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstant;
use App\Support\ApiResponse\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

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
            return ApiResponse::returnError(
                'The route is available only for users with the ADMIN role.',
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
