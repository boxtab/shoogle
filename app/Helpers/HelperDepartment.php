<?php

namespace App\Helpers;

use App\User;

/**
 * Class HelperDepartment
 * @package App\Helpers
 */
class HelperDepartment
{
    /**
     * Get an array of user ids.
     *
     * @param int|null $departmentId
     * @return array
     */
    public static function getArrayUserIds(?int $departmentId): array
    {
        if ( is_null($departmentId) ) {
            return [];
        }

        return User::on()
            ->where('department_id', '=', $departmentId)
            ->get(['id'])
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }
}
