<?php

namespace App\Helpers;

use App\Models\BuddyRequest;
use App\Models\NotificationToUser;
use App\Models\Shoogle;
use App\User;

/**
 * Class HelperNotificationBuddy
 * @package App\Helpers
 */
class HelperNotificationBuddy
{
    /**
     * From whom the notification and in which shoogle.
     *
     * @param int|null $notificationId
     * @return array|null
     */
    public static function getBuddyAndShoogle(?int $notificationId)
    {
        if ( is_null($notificationId) ) {
            return null;
        }

        $notificationToUser = NotificationToUser::on()
            ->where('id', '=', $notificationId)
            ->first();

        if ( is_null($notificationToUser) ) {
            return null;
        }

        if ( is_null($notificationToUser->shoogle_id) || is_null($notificationToUser->from_user_id) ) {
            return null;
        }

        return [
            'buddy' => self::getBuddy($notificationToUser->from_user_id),
            'shoogle' => self::getShoogle($notificationToUser->shoogle_id),
            'message' => $notificationToUser->from_message,
        ];
    }

    /**
     * From whom notification.
     *
     * @param int|null $userId
     * @return array|null
     */
    private static function getBuddy(?int $userId)
    {
        if ( is_null($userId) ) {
            return null;
        }

        $buddy = User::on()->where('id', '=', $userId)->first();

        if ( is_null($buddy) ) {
            return null;
        }

        return [
            'id' => $buddy->id,
            'profileImage' => HelperAvatar::getURLProfileImage( $buddy->profile_image ),
            'firstName' => $buddy->first_name,
            'lastName' => $buddy->last_name,
            'about' => $buddy->about,
        ];
    }

    /**
     * What is the shoogle notification.
     *
     * @param int|null $shoogleId
     * @return array|null
     */
    private static function getShoogle(?int $shoogleId)
    {
        if ( is_null($shoogleId) ) {
            return null;
        }

        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();

        if ( is_null($shoogle) ) {
            return null;
        }

        return [
            'id' => $shoogle->id,
            'title' => $shoogle->title,
            'coverImage' => $shoogle->cover_image,
        ];
    }

}
