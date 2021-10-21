<?php

namespace App\Repositories;

use App\Constants\NotificationsTypeConstant;
use App\Models\NotificationToUser;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationToUserRepository
 * @package App\Repositories
 */
class NotificationToUserRepository extends Repositories
{
    /**
     * @var NotificationToUser
     */
    protected $model;

    /**
     * NotificationToUserRepository constructor.
     * @param NotificationToUser $model
     */
    public function __construct(NotificationToUser $model)
    {
        parent::__construct($model);
    }

    /**
     * The entire list of notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getList()
    {
        return $this->model->on()
            ->leftJoin('users', 'users.id', '=', 'notifications_to_user.user_id')
            ->leftJoin('notifications_type', 'notifications_type.id', '=', 'notifications_to_user.type_id')
            ->get([
                'notifications_to_user.id as id',
                'notifications_to_user.user_id as user_id',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'notifications_type.name as type',
                'notifications_to_user.notification as notification',
                'notifications_to_user.created_at as created_at',
            ]);
    }

    /**
     * Checking for the existence of a user.
     *
     * @param int|null $userId
     * @throws \Exception
     */
    public function checkExistenceUser(?int $userId)
    {
        if ( is_null($userId) ) {
            throw new \Exception("User ID not specified", Response::HTTP_NOT_FOUND);
        }

        $user = User::on()->where('id', '=', $userId)->first();
        if ( is_null( $user ) ) {
            throw new \Exception("User ID: $userId not found", Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Mark as read.
     *
     * @param int $userId
     */
    public function viewed(int $userId)
    {
        NotificationToUser::on()
            ->where('user_id', '=', $userId)
            ->where('type_id', '<>', NotificationsTypeConstant::BUDDY_REQUEST_ID)
            ->update([
                'viewed' => 1,
            ]);
    }

    /**
     * Get a list of notifications.
     *
     * @param int|null $userId
     * @return array|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getListNotifications(?int $userId)
    {
        if ( is_null( $userId ) ) {
            return [];
        }

        return $this->model->on()
            ->leftJoin('notifications_type', 'notifications_type.id', '=', 'notifications_to_user.type_id')
            ->where('user_id', '=', $userId)
            ->get([
                'notifications_to_user.id as id',
                'notifications_type.name as typeNotificationText',
                'notifications_to_user.created_at as createdAt',
            ]);
    }

    /**
     * Artificial removal of the notification. Sets a read mark.
     *
     * @param int $notificationId
     */
    public function delete(int $notificationId)
    {
        NotificationToUser::on()
            ->where('id', '=', $notificationId)
            ->update([
                'viewed' => 1,
            ]);
    }
}
