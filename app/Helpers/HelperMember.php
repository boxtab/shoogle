<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;

/**
 * Class HelperMember
 * @package App\Helpers
 */
class HelperMember
{
    /**
     * Whether the user is a member of the shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return bool
     */
    public static function isMember(?int $shoogleID, ?int $userID): bool
    {
        if ( is_null( $shoogleID ) || is_null( $userID ) ) {
            return false;
        }

        $owner = Shoogle::on()
            ->where('id', '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->exists();

        $userHasShoogle = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->exists();

        return ($owner || $userHasShoogle) ? true : false;
    }
}
