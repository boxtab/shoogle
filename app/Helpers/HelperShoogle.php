<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Support\Facades\Log;

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

    /**
     * Is a user a member of shoogle.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isMember(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        $ownerCount = Shoogle::on()
            ->where('id' , '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->count();
        $isOwner = ($ownerCount > 0) ? 1 : 0;


        $userHasShoogleCount = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->whereNull('left_at')
            ->exists();
        $isUserHasShoogle = ($userHasShoogleCount > 0) ? 1 : 0;

        return ( $isOwner || $isUserHasShoogle ) ? true : false;
    }
}
