<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\HelperCompany;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationToUserDeleteRequest;
use App\Http\Resources\NotificationListResource;
use App\Http\Resources\NotificationToUserResource;
use App\Models\NotificationToUser;
use App\Models\Shoogle;
use App\Repositories\NotificationToUserRepository;
use App\Repositories\RewardRepository;
use App\Scopes\NotificationToUserScope;
use App\Support\ApiResponse\ApiResponse;
use App\Traits\NotificationToUserTrait;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NotificationToUserController extends BaseApiController
{
    use NotificationToUserTrait;

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
            $currentUserCompanyId = HelperCompany::getCompanyId();
            if (is_null($currentUserCompanyId)) {
                throw new Exception('The company ID for the current user was not found.', Response::HTTP_NOT_FOUND);
            }

            $listNotificationsToUser = $this->repository->getList($currentUserCompanyId);
            $notificationToUserResource = NotificationToUserResource::collection($listNotificationsToUser);
        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData($notificationToUserResource);
    }

    /**
     * Returns the number of unread notifications
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function viewed()
    {
        try {
            $userId = Auth::id();
            $this->repository->checkExistenceUser($userId);
            $viewed = $this->repository->viewed($userId);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData(['viewedCount' => $viewed]);
    }

    /**
     * List of notifications.
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function listNotifications()
    {
        try {
            $listNotification = $this->repository->getListNotifications( Auth::id() );
            $listNotificationResource = NotificationListResource::collection($listNotification);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage());
        }

        return ApiResponse::returnData( $listNotificationResource );
    }

    /**
     * Marks multiple notifications as read.
     *
     * @param NotificationToUserDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function delete(NotificationToUserDeleteRequest $request)
    {
        try {
            $listNotificationIDsRequest = $request->notificationIDs;
            $this->checkListNotificationIDs( $listNotificationIDsRequest );
            $this->repository->delete($listNotificationIDsRequest);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData([]);
    }

    /**
     * Display the specified notification.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function show($id)
    {
        try {
            if ( is_null($id) ) {
                throw new Exception('Notification ID not passed.', Response::HTTP_NOT_FOUND);
            }

            $notification = NotificationToUser::on()
                ->where('id', '=', $id)
                ->withoutGlobalScope(NotificationToUserScope::class)
                ->first();

            if ( is_null($notification) ) {
                throw new Exception('No notification found in the database.', Response::HTTP_NOT_FOUND);
            }

            $notificationUserId = $notification->user_id;
            if ( is_null($notificationUserId) ) {
                throw new Exception('The notification does not contain a user ID.', Response::HTTP_NOT_FOUND);
            }

            $userId = Auth::id();
            if ( is_null($userId) ) {
                throw new Exception('No current authenticated user.', Response::HTTP_NOT_FOUND);
            }

            if ( $notificationUserId !== $userId ) {
                throw new Exception('The notification does not belong to the user', Response::HTTP_FORBIDDEN);
            }

            if ( ! is_null($notification->deleted_at) ) {
                throw new Exception('The notification has been removed.', Response::HTTP_NOT_FOUND);
            }

            $notificationResource = new NotificationListResource($notification);

        } catch (Exception $e) {
            return ApiResponse::returnError($e->getMessage(), $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ApiResponse::returnData($notificationResource);
    }
}
