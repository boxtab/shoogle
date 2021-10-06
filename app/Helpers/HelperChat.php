<?php

namespace App\Helpers;

use App\Models\Buddie;
use App\Models\BuddyRequest;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;

/**
 * Class HelperChat
 * @package App\Helpers
 */
class HelperChat
{
    /**
     * Get chat id for shoogle.
     *
     * @param int|null $shoogleId
     * @return string|null
     */
    public static function getShoogleChatId(?int $shoogleId): ?string
    {
        if ( is_null($shoogleId) ) {
            return null;
        }

        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();

        if ( is_null($shoogle) ) {
            return null;
        }

        return $shoogle->chat_id;
    }

    /**
     * Get the chat id of a shoogle member.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return string|null
     */
    public static function getJournalChatId(?int $shoogleId, ?int $userId): ?string
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        $userHasShoogle = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user_id', '=', $userId)
            ->first();

        if ( is_null($userHasShoogle) ) {
            return null;
        }

        return $userHasShoogle->chat_id;
    }

    /**
     * Get the chat ID by the id of the shoogle with which the user is friends.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return string|null
     */
    public static function getBuddyChatId(?int $shoogleId, ?int $userId): ?string
    {
        if ( is_null($shoogleId) || is_null($userId) ) {
            return null;
        }

        $buddy = Buddie::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', '=', $userId)
                    ->orWhere('user2_id', '=', $userId);
            })
            ->first();

        if ( is_null($buddy) ) {
            return null;
        }

        return $buddy->chat_id;
    }
}
