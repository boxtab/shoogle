<?php

namespace App\Helpers;

use App\Enums\BuddyRequestTypeEnum;
use App\Http\Requests\BuddyRequest;
use App\Models\Buddie;

/**
 * Class HelperBuddyRequest
 * @package App\Helpers
 */
class HelperBuddyRequest
{
    /**
     * Was there a friend request.
     *
     * @param int|null $shoogleId
     * @param int|null $user1Id
     * @param int|null $user2Id
     * @return bool
     */
    public static function areBuddyRequest(?int $shoogleId, ?int $user1Id, ?int $user2Id): bool
    {
        if ( is_null($shoogleId) || is_null($user1Id) || is_null($user2Id) ) {
            return true;
        }

        return BuddyRequest::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('type', '<>', BuddyRequestTypeEnum::DISCONNECT)
            ->orWhere(function ($query) use ($user1Id, $user2Id) {
                $query->where('user1_id', '=', $user1Id)
                    ->where('user2_id', '=', $user2Id);
            })
            ->orWhere(function ($query) use ($user1Id, $user2Id) {
                $query->where('user2_id', '=', $user1Id)
                    ->where('user1_id', '=', $user2Id);
            })
            ->exists();
    }
}
