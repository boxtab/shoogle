<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\HelperNotifications;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Http\Request;

/**
 * Class CompanyController.
 *
 * @package App\Http\Controllers\API\V1
 */
class WebhookController extends BaseApiController
{
    public function __construct()
    {
    }

    /**
     * @param Request $apiRequest
     * @return string
     */
    public function index(Request $apiRequest): string
    {
        $helper = new HelperNotifications();
        $channel = (array)$apiRequest->json()->get('channel');
        $sender = (array)$apiRequest->json()->get('user');
        $message = (array)$apiRequest->json()->get('message');
        $users = $message['mentioned_users'];
        foreach ($users as $user) {
            $userId = $user['id'];
            $helper->sendNotificationToGetstreamUser($userId, $message['text'], $channel['name'], [
                'typeOfChannel' => $channel['typeofChannel'],
                'shoogleId' => $channel['shoogleId'],
                'userImage' => $sender['image'],
                'shoogleImage' => $channel['imageUrl']
            ]);
        }
        return "";
    }
}
