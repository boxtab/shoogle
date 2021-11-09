<?php

namespace App\Helpers;

use App\Constants\RoleConstant;
use App\Models\Company;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

/**
 * Class HelperCompany
 * @package App\Helpers
 */
class HelperCompany
{
    /**
     * Get an array of user ids.
     *
     * @param int|null $companyId
     * @return array
     */
    public static function getArrayUserIds(?int $companyId): array
    {
        if ( is_null($companyId) ) {
            return [];
        }

        return User::on()
            ->where('company_id', '=', $companyId)
            ->get(['id'])
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }

    /**
     * Return the company ID for the current user.
     *
     * @return int|null
     */
    public static function getCompanyId(): ?int
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
                    break;
                case RoleConstant::USER:
                case RoleConstant::COMPANY_ADMIN:
                    $companyId = Auth::user()->company_id;
                    break;
            }
        } catch ( Exception $e ) {
            return null;
        }

        $isDelete = Company::on()->where('id', '=', $companyId)->exists();
        if ( ! $isDelete ) {
            return null;
        }

        return $companyId;
    }
}
