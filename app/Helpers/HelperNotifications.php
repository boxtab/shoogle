<?php

namespace App\Helpers;

use GetStream\StreamChat\Client as StreamClient;
use GetStream\StreamChat\StreamException;

/**
 * Class HelperStream
 * @package App\Helpers
 */
class HelperNotifications
{
    public $streamClient;

    public function __construct()
    {
        $this->streamClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
    }

    /**
     * Creating a channel for shoogle.
     *
     * @throws StreamException
     */
    public function sendNotificationToUser(int $userId, string $message) {
        $listDevices = $this->streamClient->getDevices('user' . $userId);
        foreach ($listDevices['devices'] as $device) {
            if (isset($device['disabled'])) continue;
            $this->sendGCM($message, $device['id']);
        }
    }

    function sendGCM($message, $id, $data = []) {
        $data['typeOfChannel'] = 'notifications';
        $url = 'https://fcm.googleapis.com/fcm/send';

        $notificationData = array(
            'title' => 'Notification',
            'sound' => "default",
            'data' => $data,
            'body' => $message,
            'color' => "#79bc64"
        );
        $fields = array(
            'to' => $id,
            'notification' => $notificationData,
            'data' => $data,
            "priority" => "normal",
        );
        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . config('stream.server_key'),
            'Content-Type: application/json'
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
