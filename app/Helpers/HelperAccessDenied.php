<?php

namespace App\Helpers;

use App\Constants\NotificationsTypeConstant;
use App\Models\NotificationToUser;
use App\Scopes\NotificationToUserScope;
use App\Services\AccessDeniedService;

/**
 * Class HelperAccessDenied
 * @package App\Helpers
 */
class HelperAccessDenied
{
    /**
     * Send notification that access is denied.
     *
     * @param int|null $userId
     */
    public static function pushNotification(?int $userId)
    {
        $accessDeniedService = new AccessDeniedService($userId);
        $accessDeniedService->sendNotification();
    }

    /**
     * Get a list of notifications.
     *
     * @param int|null $notificationId
     * @return array|null
     */
    public static function getNotification(?int $notificationId):  ?array
    {
        if ( is_null( $notificationId ) ) {
            return null;
        }

        $notificationToUser = NotificationToUser::on()
            ->withoutGlobalScope(NotificationToUserScope::class)
            ->where('id', '=', $notificationId)
            ->where('type_id', '=', NotificationsTypeConstant::ACCESS_DENIED_ID)
            ->first();

        if ( is_null($notificationToUser) ) {
            return null;
        }

        return [
            'title' => 'You no longer have access to shoogle.',
            'message' => $notificationToUser->notification,
        ];
    }
}
