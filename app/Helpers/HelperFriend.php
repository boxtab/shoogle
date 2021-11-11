<?php

namespace App\Helpers;

use App\Models\Buddie;
use App\Models\Shoogle;
use App\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class HelperFriend
 * @package App\Helpers
 */
class HelperFriend
{
    /**
     * Return request to friend.
     *
     * @param int $shoogleId
     * @param int $user1Id
     * @param int $user2Id
     * @return Builder
     */
    private static function getFriendBuilder(int $shoogleId, int $user1Id, int $user2Id): Builder
    {
        return Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function($query) use($user1Id, $user2Id) {

                $query
                    ->where(function ($query) use($user1Id, $user2Id) {
                        $query->where('user1_id', '=', $user1Id)
                            ->where('user1_id', '=', $user2Id);
                    })
                    ->orWhere(function ($query) use($user1Id, $user2Id) {
                        $query->where('user1_id', '=', $user2Id)
                            ->where('user1_id', '=', $user1Id);
                    });

            });
    }

    /**
     * Returns a friend in shoogle.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getFriend(?int $shoogleId, ?int $userId): ?User
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        if ( ! Shoogle::on()->where('id', '=', $shoogleId)->exists() ) {
            return null;
        }

        if ( ! User::on()->where('id', '=', $userId)->exists() ) {
            return null;
        }

        $twoFriendShoogle = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', '=', $userId)
                    ->orWhere('user2_id', '=', $userId);
            })
            ->first();

        if ( is_null( $twoFriendShoogle ) ) {
            return null;
        }

        $friendId = ( $twoFriendShoogle->user1_id == $userId ) ? $twoFriendShoogle->user2_id : $twoFriendShoogle->user1_id;

        return User::on()->where('id', '=', $friendId)->first();
    }

    /**
     * The user has friend in the shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return bool
     */
    public static function haveFriend(?int $shoogleID, ?int $userID): bool
    {
        if ( is_null($shoogleID) || is_null($userID) ) {
            return false;
        }

        $buddyCount = Buddie::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->whereNull('disconnected_at')
            ->where(function ($query) use ($userID) {
                $query->where('user1_id', '=', $userID)
                    ->orWhere('user2_id', '=', $userID);
            })->count('*');

        return ( $buddyCount > 0 ) ? true : false;
    }
}
