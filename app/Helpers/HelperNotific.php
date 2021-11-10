<?php

namespace App\Helpers;

use App\Models\NotificationToUser;
use App\Models\UserHasShoogleLog;

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
     * Check mark.
     *
     * @param int|null $requestId
     * @param int|null $notificationType
     * @param bool|null $mark
     */
    public static function checkMark(?int $requestId, ?int $notificationType, ?bool $mark)
    {
        if ( is_null($requestId) || is_null($notificationType) || is_null($mark) ) {
            return;
        }

        NotificationToUser::on()
            ->where('buddy_request_id', '=', $requestId)
            ->where('type_id', '=', $notificationType)
            ->update([
                'viewed' => (int)$mark,
            ]);
    }
}
