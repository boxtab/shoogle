<?php

namespace App\Repositories;

use App\Constants\NotificationsTypeConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Models\BuddyRequest;
use App\Models\NotificationToUser;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getList(int $companyId)
    {
        return $this->model->on()
            ->join('users', 'users.id', '=', 'notifications_to_user.user_id')
            ->join('notifications_type', 'notifications_type.id', '=', 'notifications_to_user.type_id')
            ->where('users.company_id', '=', $companyId)
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
     * Returns the number of unread notifications.
     *
     * @param int $userId
     * @return int
     */
    public function viewed(int $userId): int
    {
//        NotificationToUser::on()
//            ->where('user_id', '=', $userId)
//            ->where('type_id', '<>', NotificationsTypeConstant::BUDDY_REQUEST_ID)
//            ->update([
//                'viewed' => 1,
//            ]);

        return NotificationToUser::on()
            ->where('user_id', '=', $userId)
            ->where('viewed', '=', 0)
            ->get()
            ->count();
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

        $notificationsToUserSelection = $this->model->on()
            ->leftJoin('notifications_type', 'notifications_type.id', '=', 'notifications_to_user.type_id')
            ->where('user_id', '=', $userId)
            ->where('viewed', '=', 0);

        $notificationsToUserCollection = $notificationsToUserSelection
            ->get([
                'notifications_to_user.id as id',
                'notifications_type.name as typeNotificationText',
                'notifications_to_user.created_at as createdAt',
            ]);

        $notificationsToUserSelection->update([
            'viewed' => 1,
        ]);

        return $notificationsToUserCollection;
    }

    /**
     * Artificial removal of the notification. Sets a read mark.
     *
     * @param array $listNotificationIDs
     */
    public function delete(array $listNotificationIDs)
    {
        DB::transaction(function () use ($listNotificationIDs) {

            foreach ( $listNotificationIDs as $listNotificationID ) {

                $notificationToUser = NotificationToUser::on()
                    ->where('id', '=', $listNotificationID)
                    ->first();

//                $notificationToUser->update([
//                    'viewed' => 1,
//                ]);

                if ( $notificationToUser->type_id === NotificationsTypeConstant::BUDDY_REQUEST_ID ) {

                    $buddyRequest = BuddyRequest::on()
                        ->where('id', '=', $notificationToUser->buddy_request_id)
                        ->first();

                    if (  is_null( $buddyRequest ) ) {
                        continue;
                    }

                    if ( $buddyRequest->type !== BuddyRequestTypeEnum::INVITE ) {
                        continue;
                    }

                    if ( $buddyRequest->user2_id !== Auth::id() ) {
                        continue;
                    }
                    $buddyRequestModel = new BuddyRequest();
                    $buddyRequestRepository = new BuddyRequestRepository($buddyRequestModel);
                    $buddyRequestRepository->buddyReject($buddyRequest);
                }

                $notificationToUser->delete();

            }
        });
    }
}
