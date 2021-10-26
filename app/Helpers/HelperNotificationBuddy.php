<?php

namespace App\Helpers;

use App\Models\BuddyRequest;
use App\Models\NotificationToUser;
use App\Models\Shoogle;
use App\Services\NotificationBuddyService;
use App\User;
use Illuminate\Support\Facades\Log;

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

        $notificationBuddyService = new NotificationBuddyService($notificationId);
        if ( $notificationBuddyService->isNull() ) {
            Log::info('yes null');
            return null;
        }

        return [
            'buddyRequestId'    => $notificationBuddyService->getBuddyRequestId(),
            'buddy'             => $notificationBuddyService->getBuddy(),
            'shoogle'           => $notificationBuddyService->getShoogle(),
            'message'           => $notificationBuddyService->getMessage(),
        ];
    }
}
