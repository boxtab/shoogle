<?php

namespace App\Helpers;

use App\Models\Buddie;
use App\Models\Shoogle;
use App\User;
use Carbon\Carbon;
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
     * @param int|null $userId
     * @return bool
     */
    public static function haveFriends(?int $shoogleId, ?int $userId): bool
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return false;
        }

        $buddyCount = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', '=', $userId)
                    ->orWhere('user2_id', '=', $userId);
            })->count('*');

        return ( $buddyCount > 0 ) ? true : false;
    }

    /**
     * Get friend ID. If a friend is not found then null.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return int|null
     */
    public static function getBuddyId(?int $shoogleId, ?int $userId): ?int
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        $buddie1 = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user2_id', '=', $userId)
            ->first('user1_id');

        if ( ! is_null( $buddie1 ) ) {
            return $buddie1->user1_id;
        }

        $buddie2 = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user1_id', '=', $userId)
            ->first('user2_id');

        if ( ! is_null( $buddie2 ) ) {
            return $buddie2->user2_id;
        }

        return null;
    }

    /**
     * Get a buddy.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getBuddy(?int $shoogleId, ?int $userId)
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        $user1Id = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user2_id', '=', $userId)
            ->first();

        if ( ! is_null( $user1Id ) ) {
            return $user1Id;
        }

        $user2Id = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user1_id', '=', $userId)
            ->first();

        if ( ! is_null( $user2Id ) ) {
            return $user2Id;
        }

        return null;
    }

    /**
     * Cancels friendship.
     *
     * @param $buddy
     */
    public static function setDisconnectedBuddy(&$buddy)
    {
        if ( ! is_null($buddy) ) {
            $buddy->disconnected = Carbon::now();
        }
    }
}
