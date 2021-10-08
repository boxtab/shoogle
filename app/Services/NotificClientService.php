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

        foreach ($lineUsers as $lineUser) {

            Log::info($lineUser['reminder_interval']);

        }
    }
}
