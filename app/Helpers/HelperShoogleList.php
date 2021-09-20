<?php

namespace App\Helpers;

use App\Models\Shoogle;

/**
 * Class HelperShoogleList
 * @package App\Helpers
 */
class HelperShoogleList
{
    /**
     * true - If the user is the creator of shoogle.
     * false - else.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isOwner(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        return Shoogle::on()
            ->where('id', '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->exists();
    }
}
