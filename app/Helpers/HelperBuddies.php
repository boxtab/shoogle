<?php

namespace App\Helpers;

use App\Models\Buddie;
use App\Models\Shoogle;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperBuddies
 * @package App\Helpers
 */
class HelperBuddies
{
    /**
     * Returns true if two users are friends within shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $user1ID
     * @param int|null $user2ID
     * @return bool
     */
    public static function isFriends(?int $shoogleID, ?int $user1ID, ?int $user2ID): bool
    {
        if ( is_null($shoogleID) || is_null($user1ID) || is_null($user2ID) ) {
            return false;
        }

        $countBuddie = Buddie::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->orWhere(function ($query) use ($user1ID, $user2ID) {
                $query->where('user1_id', '=', $user1ID)
                    ->where('user2_id', '=', $user2ID);
            })
            ->orWhere(function ($query) use ($user1ID, $user2ID) {
                $query->where('user2_id', '=', $user1ID)
                    ->where('user1_id', '=', $user2ID);
            })
            ->count();

        return ( $countBuddie > 0 ) ? true : false;
    }

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

    /**
     * Finds friend IDs within shoogle for a user.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return array
     */
    public static function getFriendsIDList(?int $shoogleID, ?int $userID): array
    {
        if ( is_null($shoogleID) || is_null($userID) ) {
            return [];
        }
        $isShoogle = Shoogle::on()
            ->where('id', '=', $shoogleID)
            ->exists();

        $isUser = User::on()
            ->where('id', '=', $userID)
            ->exists();

        if ( ! $isShoogle || ! $isUser ) {
            return [];
        }

        $buddiesIDs = Buddie::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where(function ($query) use ($userID) {
                $query->where('user1_id', '=', $userID)
                    ->orWhere('user2_id', '=', $userID);
            })
            ->get()
            ->map(function ($item) use($userID) {
                return $item['user1_id'] === $userID ? $item['user2_id'] : $item['user1_id'];
            })
            ->toArray();

        return array_unique($buddiesIDs, SORT_NUMERIC);
    }

    /**
     * Get friend ID within shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return int|null
     */
    public static function getFriendID(?int $shoogleID, ?int $userID): ?int
    {
        if ( is_null($shoogleID) || is_null($userID) ) {
            return null;
        }

        $friend = Buddie::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where(function ($query) use ($userID) {
                $query->where('user1_id', '=', $userID)
                    ->orWhere('user2_id', '=', $userID);
            })
            ->first();

        if ( is_null($friend) ) {
            return null;
        }

        return $friend->user1_id == $userID ? $friend->user2_id : $friend->user1_id;
    }
}
