<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\HelperNotifications;
use App\Http\Controllers\API\BaseApiController;
use App\Services\StreamService;
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
        $typeofChannel = $channel['typeofChannel'];
        if ($typeofChannel == "buddy") {
            $userToIgnore = $sender['id'];
            $users = StreamService::getChannelMembers($channel['id']);
            foreach ($users['members'] as $user) {
                $userId = $user['user_id'];
                if ($userId == $userToIgnore) {
                    continue;
                }
                $helper->sendNotificationToGetstreamUser($userId, $message['text'], $channel['name'], [
                    'typeOfChannel' => $typeofChannel,
                    'shoogleId' => $channel['shoogleId'],
                    'userImage' => $sender['image'],
                    'shoogleImage' => $channel['imageUrl']
                ]);
            }
        } else {
            $users = $message['mentioned_users'];
            foreach ($users as $user) {
                $userId = $user['id'];
                $helper->sendNotificationToGetstreamUser($userId, $message['text'], $channel['name'], [
                    'typeOfChannel' => $typeofChannel,
                    'shoogleId' => $channel['shoogleId'],
                    'userImage' => $sender['image'],
                    'shoogleImage' => $channel['imageUrl']
                ]);
            }
        }
        return "";
    }
}
