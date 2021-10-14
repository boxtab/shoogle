<?php

namespace App\Helpers;

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
}
