<?php

namespace App\Helpers;

use App\Models\Buddie;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperBuddies
 * @package App\Helpers
 */
class HelperBuddies
{
    /**
     * The user has friends in the shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return bool
     */
    public static function haveFriends(?int $shoogleID, ?int $userID): bool
    {
        if ( is_null($shoogleID) || is_null($userID) ) {
            return false;
        }

        $buddyCount = Buddie::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where(function ($query) use ($userID) {
                $query->where('user1_id', '=', $userID)
                    ->orWhere('user2_id', '=', $userID);
            })->count('*');

        return ( $buddyCount > 0 ) ? true : false;
    }
}
