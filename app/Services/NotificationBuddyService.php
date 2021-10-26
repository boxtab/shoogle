<?php

namespace App\Services;

use App\Constants\NotificationsTypeConstant;
use App\Helpers\HelperAvatar;
use App\Models\BuddyRequest;
use App\Models\Notification;
use App\Models\NotificationToUser;
use App\Models\Shoogle;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationBuddyService
 * @package App\Services
 */
class NotificationBuddyService
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * NotificationBuddyService constructor.
     * @param int|null $notificationId
     */
    public function __construct( ?int $notificationId )
    {
        $this->notification = NotificationToUser::on()
            ->where('id', '=', $notificationId)
            ->first();
    }

    /**
     * Is there a notification.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        if ( is_null( $this->notification ) ) {
            return true;
        }

        $response = false;

        if (
             $this->notification->type_id === NotificationsTypeConstant::BUDDY_REQUEST_ID ||
             $this->notification->type_id === NotificationsTypeConstant::BUDDY_CONFIRM_ID ||
             $this->notification->type_id === NotificationsTypeConstant::BUDDY_REJECT_ID ||
             $this->notification->type_id === NotificationsTypeConstant::BUDDY_DISCONNECT_ID
        ) {
            $response = true;
        }

        if ( is_null( $this->notification->shoogle_id ) ) {
            $response = true;
        }

        if ( is_null( $this->notification->from_user_id ) ) {
            $response = true;
        }

        return $response;
    }

    /**
     * Get buddy request id.
     *
     * @return int|null
     */
    public function getBuddyRequestId(): ?int
    {
        $userId = $this->notification->user_id;
        $fromUserId = $this->notification->from_user_id;

        $buddyRequest = BuddyRequest::on()
            ->where('shoogle_id', '=', $this->notification->shoogle_id)
            ->where(function ($query) use ($userId, $fromUserId) {

                $query->where(function ($query) use ($userId, $fromUserId) {
                    $query->where('user1_id', '=', $userId)
                        ->where('user2_id', '=', $fromUserId);
                })
                ->orWhere(function ($query) use ($userId, $fromUserId) {
                    $query->where('user1_id', '=', $fromUserId)
                        ->where('user2_id', '=', $userId);
                });

            })->first();

        if ( is_null($buddyRequest) ) {
            return null;
        }

        return $buddyRequest->id;
    }

    /**
     * From whom notification.
     *
     * @return array|null
     */
    public function getBuddy(): ?array
    {
        if ( is_null( $this->notification ) ) {
            return null;
        }

        $buddy = User::on()->where('id', '=', $this->notification->from_user_id)->first();

        if ( is_null($buddy) ) {
            return null;
        }

        return [
            'id' => $buddy->id,
            'profileImage' => HelperAvatar::getURLProfileImage( $buddy->profile_image ),
            'firstName' => $buddy->first_name,
            'lastName' => $buddy->last_name,
            'about' => $buddy->about,
        ];
    }

    /**
     * What is the shoogle notification.
     *
     * @return array|null
     */
    public function getShoogle(): ?array
    {
        if ( is_null( $this->notification ) ) {
            return null;
        }

        $shoogle = Shoogle::on()->where('id', '=', $this->notification->shoogle_id)->first();

        if ( is_null($shoogle) ) {
            return null;
        }

        return [
            'id' => $shoogle->id,
            'title' => $shoogle->title,
            'coverImage' => $shoogle->cover_image,
        ];
    }

    /**
     * Message from the one who sent the notification.
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->notification->from_message;
    }
}
