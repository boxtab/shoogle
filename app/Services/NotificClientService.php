<?php

namespace App\Services;

use App\Helpers\HelperNotific;
use App\Helpers\HelperNotifications;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificClientService
 * @package App\Services
 */
class NotificClientService
{
    /**
     * Start sending notifications.
     *
        'id',
        'user_id',
        'shoogle_id',
        'reminder',
        'reminder_interval',
        'last_notification',
        'in_process',
     *
     * @throws \Exception
     */
    public function run(): void
    {
        $notificService = new NotificService();
        $lineUsers = $notificService->getLineUsers();

        $userHasShoogleIds = $notificService->getUserHasShoogleIds($lineUsers);
        $notificService->lockUserHasShoogle($userHasShoogleIds);

        foreach ($lineUsers as $lineUser) {
            $needToSend = $notificService->needToSend($lineUser['reminder'], $lineUser['reminder_interval'], $lineUser['last_notification']);
            if ( $needToSend ) {

                $helper = new HelperNotifications();
                $helper->sendNotificationToUser($lineUser['user_id'], 'testNotification2');

                /*
                 * Do not delete, temporarily commented out.
                 */
//                HelperNotific::push($lineUser['user_id'], $lineUser['shoogle_id'], $lineUser['id']);

                $notificService->putNowLastNotification($lineUser['id']);
            }
            $notificService->unlockUserHasShoogle($lineUser['id']);
        }
    }
}
