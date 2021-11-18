<?php

namespace App\Helpers;

use App\Constants\NotificationsTypeConstant;
use App\Models\NotificationToUser;
use App\Models\Shoogle;
use App\Models\UserHasShoogleLog;
use App\Scopes\NotificationToUserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperNotific
 * @package App\Helpers
 */
class HelperNotific
{
    /**
     * Send notification.
     *
     * @param int|null $userId
     * @param int|null $shoogleId
     * @param int|null $userHasShoogleId
     */
    public static function push(?int $userId, ?int $shoogleId, ?int $userHasShoogleId)
    {
        UserHasShoogleLog::on()->create([
            'user_id' => $userId,
            'shoogle_id' => $shoogleId,
            'user_has_shoogle_id' => $userHasShoogleId,
            'created_at' => HelperNow::getCarbon(),
            'updated_at' => HelperNow::getCarbon(),
        ]);
    }

    /**
     * Remove friend request notification.
     *
     * @param int|null $requestId
     */
    public static function deleteNotificationBuddyRequest(?int $requestId)
    {
        if ( is_null($requestId) ) {
            return;
        }

        NotificationToUser::on()
            ->withoutGlobalScope(NotificationToUserScope::class)
            ->where('buddy_request_id', '=', $requestId)
            ->where('type_id', '=', NotificationsTypeConstant::BUDDY_REQUEST_ID)
            ->delete();
    }

    /**
     * Check notification.
     *
     * @param int|null $notificationId
     * @param int|null $userId
     * @param int $notificationType
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function checkNotification(?int $notificationId, ?int $userId, int $notificationType)
    {
        if ( is_null($notificationId) || is_null($userId) ) {
            return null;
        }

        $notification = NotificationToUser::on()
            ->withoutGlobalScope(NotificationToUserScope::class)
            ->where('id', '=', $notificationId)
            ->first();

        if ( is_null( $notification ) ) {
            return null;
        }

        if ( $notification->user_id !== $userId ) {
            return null;
        }

        if ( $notification->type_id !== $notificationType ) {
            return null;
        }

        return $notification;
    }

    /**
     * Reminder from the shoogle planner.
     *
     * @param int|null $notificationId
     * @param int|null $userId
     * @return array|null
     */
    public static function getRemainderScheduler(?int $notificationId, ?int $userId)
    {
        $notification = self::checkNotification($notificationId, $userId, NotificationsTypeConstant::SHOOGLE_REMIDER_ID);
        if ( is_null($notification) ) {
            return null;
        }

        $shoogleName = null;
        $coverImage = null;
        $shoogle = Shoogle::on()->where('id', '=', $notification->shoogle_id)->first();
        if ( ! is_null($shoogle) ) {
            $shoogleName = $shoogle->title;
            $coverImage = $shoogle->cover_image;
        } else {
            return null;
        }

        return [
            'shoogleId'     => $notification->shoogle_id,
            'coverImage'    => $coverImage,
            'title'         => 'shoogle reminder',
            'shoogleName'   => $shoogleName,
        ];
    }
}
