<?php

namespace App\Services;

use App\Helpers\HelperNotific;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificClientService
 * @package App\Services
 */
class NotificClientService
{
    /**
     * Start sending notifications.
     */
    public function run(): void
    {
        $notificService = new NotificService();
        $lineUsers = $notificService->getLineUsers();

        $userHasShoogleId = $notificService->getUserHasShoogleIds($lineUsers);
        $notificService->lockUserHasShoogle($userHasShoogleId);

        foreach ($lineUsers as $lineUser) {

            $needToSend = $notificService->needToSend($lineUser['reminder'], $lineUser['reminder_interval'], $lineUser['last_notification']);
            if ( $needToSend ) {
                HelperNotific::push($lineUser['user_id'], $lineUser['shoogle_id'], $lineUser['id']);
                $notificService->putNowLastNotification($lineUser['id']);
            }
        }

        $notificService->unlockUserHasShoogle($userHasShoogleId);
//        'id',
//        'user_id',
//        'shoogle_id',
//        'reminder',
//        'reminder_interval',
//        'last_notification',
//        'in_process',
    }
}
