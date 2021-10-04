<?php

namespace App\Helpers;

/**
 * Class HelperBuddyRequest
 * @package App\Helpers
 */
class HelperBuddyRequest
{
    /**
     * Was there a friend request.
     *
     * @param int|null $shoogleID
     * @param int|null $user1ID
     * @param int|null $user2ID
     * @return bool
     */
    public static function areBuddyRequest(?int $shoogleID, ?int $user1ID, ?int $user2ID): bool
    {
        if ( is_null($shoogleID) || is_null($user1ID) || is_null($user2ID) ) {
            return true;
        }

        return false;
    }
}
