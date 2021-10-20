<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationListResource;
use App\Http\Resources\NotificationToUserResource;
use App\Models\NotificationToUser;
use App\Repositories\NotificationToUserRepository;
use App\Repositories\RewardRepository;
use App\Support\ApiResponse\ApiResponse;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NotificationToUserController extends BaseApiController
{
    /**
     * NotificationToUserController constructor.
     * @param NotificationToUserRepository $notificationToUserRepository
     */
    public function __construct(NotificationToUserRepository $notificationToUserRepository)
    {
        $this->repository = $notificationToUserRepository;
    }

    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function index()
    {
        try {
            $listNotificationsToUser = $this->repository->getList();
            $notificationToUserResource = NotificationToUserResource::collection($listNotificationsToUser);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData($notificationToUserResource);
    }

    /**
     * Marks all notifications as read.
     *
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function viewed($userId)
    {
        try {
            $this->repository->checkExistenceUser($userId);
            $this->repository->viewed($userId);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * List of notifications.
     */
    public function listNotifications()
    {
        try {
            $listNotification = $this->repository->getListNotifications( Auth::id() );
            $listNotificationResource = NotificationListResource::collection($listNotification);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData( $listNotificationResource );
    }

}
