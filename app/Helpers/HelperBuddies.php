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
}
