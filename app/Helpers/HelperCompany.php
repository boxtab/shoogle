<?php

namespace App\Helpers;

use App\Constants\RoleConstant;
use App\Models\Company;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * Get company ID by user ID.
     *
     * @param int|null $userId
     * @return int|null
     */
    public static function getCompanyIdByUserId(?int $userId): ?int
    {
        if ( is_null( $userId ) ) {
            return null;
        }

        $user = User::on()->where('id', '=', $userId)->first();
        if ( is_null( $user ) ) {
            return null;
        }

        return $user->company_id;
    }

    /**
     * Get administrator ID by company ID.
     *
     * @param int|null $companyId
     * @return int|null
     */
    public static function getAdminIdByCompanyId(?int $companyId): ?int
    {
        if ( is_null( $companyId ) ) {
            return null;
        }

        $userIDs = User::on()
            ->where('company_id', '=', $companyId)
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        try {
            $companyAdminRoleId = Role::on()
                ->where('name', '=', RoleConstant::COMPANY_ADMIN)
                ->first()
                ->id;
        } catch (ModelNotFoundException $e) {
            $companyAdminRoleId = null;
        }

        $modelHasRoles = ModelHasRole::on()
            ->whereIn('model_id', $userIDs)
            ->where('role_id', '=', $companyAdminRoleId)
            ->first();
        if ( is_null($modelHasRoles) ) {
            return null;
        }

        return $modelHasRoles->model_id;
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
