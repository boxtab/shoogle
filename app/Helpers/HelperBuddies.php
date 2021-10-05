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
     * @param int|null $shoogleId
     * @param int|null $user1Id
     * @param int|null $user2Id
     * @return bool
     */
    public static function isFriends(?int $shoogleId, ?int $user1Id, ?int $user2Id): bool
    {
        if ( is_null($shoogleId) || is_null($user1Id) || is_null($user2Id) ) {
            return false;
        }

        $countBuddie = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function ($query) use ($user1Id, $user2Id) {

                $query->where(function ($query) use ($user1Id, $user2Id) {
                    $query->where('user1_id', '=', $user1Id)
                        ->where('user2_id', '=', $user2Id);
                })
                ->orWhere(function ($query) use ($user1Id, $user2Id) {
                    $query->where('user2_id', '=', $user1Id)
                        ->where('user1_id', '=', $user2Id);
                });

            })
            ->count();

        return ( $countBuddie > 0 ) ? true : false;
    }

    /**
     * The user has friends in the shoogle.
     *
     * @param int|null $shoogleId
     * @param int|null $userID
     * @return bool
     */
    public static function haveFriends(?int $shoogleId, ?int $userID): bool
    {
        if ( is_null($shoogleId) || is_null($userID) ) {
            return false;
        }

        $buddyCount = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function ($query) use ($userID) {
                $query->where('user1_id', '=', $userID)
                    ->orWhere('user2_id', '=', $userID);
            })->count('*');

        return ( $buddyCount > 0 ) ? true : false;
    }

    /**
     * Finds friend IDs within shoogle for a user.
     *
     * @param int|null $shoogleId
     * @param int|null $userID
     * @return array
     */
    public static function getFriendsIDList(?int $shoogleId, ?int $userID): array
    {
        if ( is_null($shoogleId) || is_null($userID) ) {
            return [];
        }
        $isShoogle = Shoogle::on()
            ->where('id', '=', $shoogleId)
            ->exists();

        $isUser = User::on()
            ->where('id', '=', $userID)
            ->exists();

        if ( ! $isShoogle || ! $isUser ) {
            return [];
        }

        $buddiesIDs = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
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
     * @param int|null $shoogleId
     * @param int|null $userID
     * @return int|null
     */
    public static function getFriendID(?int $shoogleId, ?int $userID): ?int
    {
        if ( is_null($shoogleId) || is_null($userID) ) {
            return null;
        }

        $friend = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
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
