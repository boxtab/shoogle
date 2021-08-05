<?php

use App\Constants\RoleConstant;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

if ( ! function_exists('pushCompanyIdToJWT') ) {

    function pushCompanyIdToJWT( int $companyId )
    {
        return JWTAuth::customClaims(['company_id' => $companyId])->fromUser( Auth::user() );
    }

}

if ( ! function_exists('getCompanyIdFromJWT') ) {

    function getCompanyIdFromJWT()
    {
        $companyId = null;
        $roleName = Auth::user()->roles()->first()->name;

        try {
            switch ($roleName) {
                case RoleConstant::SUPER_ADMIN:
                    $payload = JWTAuth::parseToken()->getPayload();
                    $companyId = $payload->get('company_id');
//                    if ( is_null( $companyId ) ) {
//                        throw new \Exception('No company selected.');
//                    }
                    break;
                case RoleConstant::COMPANY_ADMIN:
                    $companyId = Auth::user()->company_id;
                    break;
            }
        } catch ( \Exception $e ) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
            ]);
        }

        return $companyId;
    }

}
