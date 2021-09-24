<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;

/**
 * Class HelperShoogle
 * @package App\Helpers
 */
class HelperShoogle
{
    /**
     * Get all id shoogles by user id.
     *
     * @param int|null $userID
     * @return array
     */
    public static function getShooglesIDsByUserID(?int $userID): array
    {
        if ( is_null( $userID ) ) {
            return [];
        }

        $shoogleIDsFromOwner = Shoogle::on()
            ->where('owner_id', '=', $userID)
            ->get('id')
            ->map(function ($item) {
                return $item['id'];
            })
            ->toArray();

        $shooglesIDsFromMembers = UserHasShoogle::on()
            ->where('user_id', '=', $userID)
            ->get('shoogle_id')
            ->map(function ($item) {
                return $item['shoogle_id'];
            })
            ->toArray();

        return array_unique( array_merge($shoogleIDsFromOwner, $shooglesIDsFromMembers) );
    }
}
