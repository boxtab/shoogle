<?php

namespace App\Services;

use App\Constants\NotificationsTypeConstant;
use App\Constants\NotificationTextConstant;
use App\Helpers\HelperNotific;
use App\Helpers\HelperNotifications;
use App\Models\Shoogle;
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
    public function run()
    {
        $countSendNotific = 0;
        $notificService = new NotificService();
        $lineUsers = $notificService->getLineUsers();

//        Log::info($lineUsers);

        $userHasShoogleIds = $notificService->getUserHasShoogleIds($lineUsers);
        $notificService->lockUserHasShoogle($userHasShoogleIds);

        foreach ($lineUsers as $lineUser) {
            $needToSend = $notificService->needToSend(
                $lineUser['reminder'],
                $lineUser['reminder_interval'],
                $lineUser['last_notification']
            );

            if ( $needToSend ) {

                $shoogle = Shoogle::on()
                    ->where('id', '=', $lineUser['shoogle_id'])
                    ->first();
                $shoogleTitle = ( ! is_null($shoogle) ) ? $shoogle->title : null;
                $shoogleId = ( ! is_null($shoogle) ) ? $shoogle->id : null;

                $helper = new HelperNotifications();
                $helper->sendNotificationToUser(
                    $lineUser['user_id'],
                    NotificationsTypeConstant::SCHEDULER_ID,
                    $shoogleTitle
                );
                $helper->recordNotificationDetail($shoogleId);
                $countSendNotific++;
                /*
                 * Do not delete, temporarily commented out.
                 */
//                HelperNotific::push($lineUser['user_id'], $lineUser['shoogle_id'], $lineUser['id']);

                $notificService->putNowLastNotification($lineUser['id']);
            }
            $notificService->unlockUserHasShoogle($lineUser['id']);
        }

        return $countSendNotific;
    }
}
