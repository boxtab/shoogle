<?php

namespace App\Helpers;

use App\Enums\BuddyRequestTypeEnum;
use App\Models\BuddyRequest;
use App\Models\Buddie;
use Illuminate\Support\Facades\Log;

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
    public static function isBuddyRequest(?int $shoogleId, ?int $user1Id, ?int $user2Id): bool
    {
        if ( is_null($shoogleId) || is_null($user1Id) || is_null($user2Id) ) {
            return true;
        }

        $countBuddyRequest = BuddyRequest::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('type', '<>', BuddyRequestTypeEnum::DISCONNECT)
            ->where(function ($query) use ($user1Id, $user2Id) {

                $query->where(function ($query) use ($user1Id, $user2Id) {
                    $query->where('user1_id', '=', $user1Id)
                        ->where('user2_id', '=', $user2Id); })
                    ->orWhere(function ($query) use ($user1Id, $user2Id) {
                        $query->where('user2_id', '=', $user1Id)
                            ->where('user1_id', '=', $user2Id);
                    });

            })
            ->count();

        return ( $countBuddyRequest > 0 ) ? true : false;
    }

    /**
     * Returns true if the invite is still valid.
     *
     * @param int|null $buddyRequestId
     * @return bool
     */
    public static function isActualInvite(?int $buddyRequestId): bool
    {
        if ( is_null($buddyRequestId) ) {
            return false;
        }

        $countBuddyRequest = BuddyRequest::on()
            ->where('id', '=', $buddyRequestId)
            ->where('type', '=', BuddyRequestTypeEnum::INVITE)
            ->count();

        return ( $countBuddyRequest > 0 ) ? true : false;
    }

    public static function getNotification(?int $notificationId)
    {
        if ( is_null($notificationId) ) {
            return null;
        }

        return BuddyRequest::on();
    }
}
