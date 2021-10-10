<?php

namespace App\Services;

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
//        $notificService->unlockUserHasShoogle($userHasShoogleId);

        dd($userHasShoogleId);
//        dd($lineUsers);

//        foreach ($lineUsers as $lineUser) {
//
//            Log::info($lineUser['reminder_interval']);
//
//        }
    }
}
