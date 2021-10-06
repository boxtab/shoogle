<?php

namespace App\Helpers;

use App\Models\Buddie;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShoogleStatistic
 * @package App\Helpers
 */
class HelperShoogleStatistic
{
    /**
     * Returns an array of members.
     *
     * @param int|null $shoogleID
     * @return array
     */
    private static function getMembers(?int $shoogleID): array
    {
        if ( is_null($shoogleID) ) {
            return [];
        }

        if ( Shoogle::on()->where('id', '=', $shoogleID)->count() === 0) {
            return [];
        }

        $ownerID = Shoogle::on()->where('id', '=', $shoogleID)->first('owner_id')->owner_id;
        $userHasShoogle = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->get('user_id')
            ->map(function ($item) {
                return $item['user_id'];
            })
            ->toArray();

        return array_unique( array_merge([$ownerID], $userHasShoogle) );
    }

    /**
     * Returns the number of members for a shoogle.
     *
     * @param int|null $shoogleID
     * @return int
     */
    public static function getShooglersCount(?int $shoogleID): int
    {
        $members = self::getMembers($shoogleID);

        return count($members);
    }

    /**
     * Returns the number of members for a shoogle who have a friend.
     *
     * @param int|null $shoogleId
     * @return int
     */
    public static function getBuddiesCount(?int $shoogleId): int
    {
        if ( is_null($shoogleId) ) {
            return 0;
        }
        $members = HelperMember::getListMemberIDs($shoogleId);

        $buddiesCount = 0;
        foreach ($members as $member) {
            $buddiesCount += (int)HelperBuddies::haveFriends($shoogleId, $member);
        }

        return $buddiesCount;
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

        return UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('solo', '<>', 0)
            ->count();
    }
}
