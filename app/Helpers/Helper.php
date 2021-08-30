<?php

namespace App\Helpers;

use App\Models\ModelHasRole;
use Illuminate\Support\Facades\Log;
use stdClass;
use App\Constants\RoleConstant;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

/**
 * Class Helper.
 *
 * @package App\Helpers
 */
class Helper
{
    /**
     * Makes a string out of a set of array elements.
     *
     * @param $objectContainsArrays
     * @return stdClass
     */
    public static function replaceArraysOnStrings( $objectContainsArrays )
    {
        if ( gettype( $objectContainsArrays ) === 'string' ) {
            return $objectContainsArrays;
        }

        $objectContainsStrings = new stdClass();
        foreach ($objectContainsArrays->toArray() as $key => $value) {
            $objectContainsStrings->$key = implode($value);
        }
        return $objectContainsStrings;
    }

    /**
     * Push the company ID into the token.
     *
     * @param int $companyId
     * @return mixed
     */
    public static function pushCompanyIdToJWT( int $companyId )
    {
        return JWTAuth::customClaims(['company_id' => $companyId])->fromUser( Auth::user() );
    }

    /**
     * Get the company ID from the token.
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public static function getCompanyIdFromJWT()
    {
        if ( Auth::guest() ) {
            return null;
        }

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

    /**
     * Convert array keys from camelCase to snake_case.
     *
     * @param array $request
     * @return array
     */
    public static function formatSnakeCase(array $request): array
    {
        $replaced = [];
        foreach ($request as $key => $field) {
            $replaced[Str::snake($key)] = $field;
        }
        return $replaced;
    }

    /**
     * Get a link to reset your password.
     *
     * @param string $token
     * @param string $email
     * @return string
     */
    public static function getLinkResetPassword(string $token, string $email): string
    {
        $host = request()->getSchemeAndHttpHost();
        return "$host/reset-password/:$token/:$email";
    }

    /**
     * Returns the text name of the role.
     *
     * @param int $userId
     * @return string
     */
    public static function getRole(int $userId): string
    {
        return ModelHasRole::where('model_id', $userId)
            ->first('role_id')
            ->role
            ->name;
    }
}
