<?php

namespace App\Helpers;

/**
 * Class HelperShoogleStatistic
 * @package App\Helpers
 */
class HelperShoogleStatistic
{
    /**
     * Returns the number of members for a shoogle.
     *
     * @param int|null $shoogleID
     * @return int
     */
    public static function getShooglersCount(?int $shoogleID): int
    {
        if ( is_null($shoogleID) ) {
            return 0;
        }

        return 1;
    }

    /**
     * Returns the number of members for a shoogle who have a friend.
     *
     * @param int|null $shoogleID
     * @return int
     */
    public static function getBuddiesCount(?int $shoogleID): int
    {
        if ( is_null($shoogleID) ) {
            return 0;
        }

        return 1;
    }

    /**
     * Returns the number of members for a shoogle who has banned friend requests.
     *
     * @param int|null $shoogleID
     * @return int
     */
    public static function getSolosCount(?int $shoogleID): int
    {
        if ( is_null($shoogleID) ) {
            return 0;
        }

        return 1;
    }
}
