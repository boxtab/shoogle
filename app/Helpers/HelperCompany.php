<?php

namespace App\Helpers;

use App\User;

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
}
