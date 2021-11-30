<?php

namespace App\Helpers;

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
}
