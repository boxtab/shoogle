<?php

namespace App\Helpers;

use App\Models\NotificationToUser;
use GetStream\StreamChat\Client as StreamClient;
use GetStream\StreamChat\StreamException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperStream
 * @package App\Helpers
 */
class HelperNotifications
{
    /**
     * @var StreamClient
     */
    public $streamClient;

    /**
     * @var NotificationToUser
     */
    private $notificationToUser = null;

    /**
     * HelperNotifications constructor.
     */
    public function __construct()
    {
        $this->streamClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
    }

    /**
     * Creating a channel for shoogle.
     *
     * @param int $userId
     * @param int $typeId
     * @param string $message
     * @throws StreamException
     */
    public function sendNotificationToUser(int $userId, int $typeId, string $message = '')
    {
        $listDevices = $this->streamClient->getDevices('user' . $userId);
        $notificationId = $this->recordNotification($userId, $typeId, $message);
        foreach ($listDevices['devices'] as $device) {
            if (isset($device['disabled'])) continue;
            $this->sendGCM($message, $device['id'], ['notificationId' => (string)$notificationId]);
        }
    }

    /**
     * @param $message
     * @param $id
     * @param array $data
     * @throws \Exception
     */
    private function sendGCM($message, $id, $data = [])
    {
        $data['typeOfChannel'] = 'notifications';
        $url = 'https://fcm.googleapis.com/fcm/send';

        $notificationData = array(
            'title' => 'Notification',
            'data' => $data,
            'body' => $message,
//            'image' => 'https://images.unsplash.com/photo-1633857275114-4effece78b0c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwyNTg5OTl8MHwxfHJhbmRvbXx8fHx8fHx8fDE2MzM5NDYwMDc&ixlib=rb-1.2.1&q=80&w=400&w=1080&h=720'
        );
        $fields = array(
            'to' => $id,
            'notification' => $notificationData,
            'data' => $data,
            'content-available' => true,
            'priority' => "high",
            'badge' => 1,
            "apns-priority" => "10",
        );
        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . config('stream.server_key'),
            'Content-Type: application/json'
        );

        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            throw new \Exception('Function RsendGCM error.', Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * Record notification.
     *
     * @param int|null $userId
     * @param int|null $typeId
     * @param string|null $message
     * @return int|mixed|null
     */
    private function recordNotification(?int $userId, ?int $typeId, ?string $message)
    {
        if ( is_null( $userId ) || is_null( $typeId ) ) {
            return null;
        }

        $this->notificationToUser = NotificationToUser::on()->create([
            'user_id' => $userId,
            'type_id' => $typeId,
            'notification' => $message,
        ]);
        return $this->notificationToUser->id;
    }

    /**
     * Get notification ID.
     *
     * @return int
     */
    public function getNotificationToUserId(): int
    {
        return $this->notificationToUser->id;
    }

    /**
     * Recording additional information for notifications.
     *
     * @param int|null $shoogleId
     * @param int|null $fromUserId
     * @param string|null $fromMessage
     * @param int|null $buddyRequestId
     * @param int|null $buddyId
     * @return null
     */
    public function recordNotificationDetail(
        ?int $shoogleId = null,
        ?int $fromUserId = null,
        ?string $fromMessage = null,
        ?int $buddyRequestId = null,
        ?int $buddyId = null)
    {
        if ( is_null( $this->notificationToUser ) ) {
            return null;
        }

        $this->notificationToUser->update([
            'shoogle_id'        => $shoogleId,
            'from_user_id'      => $fromUserId,
            'from_message'      => $fromMessage,
            'buddy_request_id'  => $buddyRequestId,
            'buddy_id'          => $buddyId,
        ]);

        return null;
    }
}
