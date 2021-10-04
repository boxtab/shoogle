<?php

namespace App\Helpers;

use App\Models\UserHasShoogle;

/**
 * Class HelperCalendar
 * @package App\Helpers
 */
class HelperCalendar
{
    /**
     * If the user participates in shugla, then we determine whether he allows him to be friends.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return bool|null
     */
    public static function getBuddy(?int $shoogleId, ?int $userId): ?bool
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        $member = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user_id', '=', $userId)
            ->first();

        if ( $member == false ) {
            return null;
        }

        return ( ! $member->solo ) ? true : false;
    }
}
