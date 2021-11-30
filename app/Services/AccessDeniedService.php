<?php

namespace App\Services;

use App\Constants\NotificationsTypeConstant;
use App\Constants\NotificationTextConstant;
use App\Helpers\HelperNotifications;
use App\User;

/**
 * Class AccessDeniedService
 * @package App\Services
 */
class AccessDeniedService
{
    /**
     * @var int|null
     */
    private $userId;

    /**
     * AccessDeniedService constructor.
     * @param int|null $userId
     */
    public function __construct(?int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Conditions of performance.
     *
     * @return bool
     */
    private function conditionsPerformance(): bool
    {
        $user = User::withTrashed()->where('id', '=', $this->userId)->first();

        return ! is_null($user) ? true : false;
    }

    /**
     * Sending a notification that the user does not have access.
     */
    public function sendNotification()
    {
        if ( ! $this->conditionsPerformance() ) {
            return;
        }

        $helperNotification = new HelperNotifications();
        $helperNotification->sendNotificationToUser(
            $this->userId,
            NotificationsTypeConstant::ACCESS_DENIED_ID,
            NotificationTextConstant::ACCESS_DENIED
        );
    }
}
