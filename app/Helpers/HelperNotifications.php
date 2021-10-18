<?php

namespace App\Helpers;

use App\Models\NotificationToUser;
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
     * @param int $userId
     * @param string $message
     * @throws StreamException
     */
    public function sendNotificationToUser(int $userId, string $message)
    {
        $listDevices = $this->streamClient->getDevices('user' . $userId);
        foreach ($listDevices['devices'] as $device) {
            if (isset($device['disabled'])) continue;
            $this->sendGCM($message, $device['id']);
        }

        $this->recordNotification($userId, $message);
    }

    /**
     * @param $message
     * @param $id
     * @param array $data
     */
    private function sendGCM($message, $id, $data = [])
    {
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

    /**
     * Record notification.
     *
     * @param int|null $userId
     * @param string|null $message
     */
    private function recordNotification(?int $userId, ?string $message): void
    {
        if ( is_null( $userId ) ) {
            return null;
        }

        NotificationToUser::on()->create([
            'user_id' => $userId,
            'notification' => $message,
        ]);
    }
}
