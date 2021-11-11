<?php

namespace App\Repositories;

use App\Constants\NotificationsTypeConstant;
use App\Constants\NotificationTextConstant;
use App\Constants\RoleConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\Helper;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperMember;
use App\Helpers\HelperNotific;
use App\Helpers\HelperNotifications;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperUser;
use App\Models\BuddyRequest;
use App\Models\Company;
use App\Models\UserHasShoogle;
use App\Scopes\BuddiesScope;
use App\Models\Shoogle;
use App\Services\StreamService;
use App\Traits\UserCompanyTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Buddie;
use PhpParser\Builder;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

/**
 * Class BuddyRequestRepository
 * @package App\Repositories
 */
class BuddyRequestRepository extends Repositories
{
    use UserCompanyTrait;

    /**
     * @var BuddyRequest
     */
    protected $model;

    /**
     * BuddyRequestRepository constructor.
     * @param BuddyRequest $model
     */
    public function __construct(BuddyRequest $model)
    {
        parent::__construct($model);
    }

    /**
     * Friend request.
     *
     * @param int $shoogleId
     * @param int $user2Id
     * @param string|null $message
     * @throws \Exception
     */
    public function buddyRequest(int $shoogleId, int $user2Id, ?string $message)
    {
        $user1Id = Auth::id();

        if ( ! HelperMember::isMember($shoogleId, $user1Id) ) {
            throw new \Exception("You are not a member of shoogle!",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( ! HelperMember::isMember($shoogleId, $user2Id) ) {
            throw new \Exception("The user with whom you want to be friends is not a member of shoogle!",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( HelperBuddies::isFriends($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception("You are already friends with this user!",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( HelperBuddyRequest::isBuddyRequest($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception('You have already sent a request to this user!',
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::transaction(function () use ($shoogleId, $user1Id, $user2Id, $message) {

            $buddyRequest = BuddyRequest::on()->updateOrCreate(
                [
                    'shoogle_id'    => $shoogleId,
                    'user1_id'      => $user1Id,
                    'user2_id'      => $user2Id,
                ],
                [
                    'type'          => BuddyRequestTypeEnum::INVITE,
                    'message'       => $message,
                ]
            );

            Buddie::on()
                ->where('shoogle_id', '=', $shoogleId)
                ->where('user1_id', '=', $user1Id)
                ->where('user2_id', '=', $user2Id)
                ->withoutGlobalScope(BuddiesScope::class)
                ->whereNotNull('disconnected_at')
                ->update(['disconnected_at' => null]);

            $helperNotification = new HelperNotifications();

            $userName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $helperNotification->sendNotificationToUser(
                $user2Id,
                NotificationsTypeConstant::BUDDY_REQUEST_ID,
                ( ! is_null( $message ) ) ? $message : "$userName has invited you to buddy up"
            );
            $helperNotification->recordNotificationDetail($shoogleId, $user1Id, $message, $buddyRequest->id);

        });
    }

    /**
     * Requests received.
     *
     * @param int $page
     * @param int $pageSize
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function buddyReceived(int $page, int $pageSize)
    {
        return BuddyRequest::on()
            ->select(DB::raw('
                id as id,
                user1_id as buddy,
                shoogle_id as shoogle_id,
                created_at as created_at,
                message as message
            '))
            ->where('user2_id', Auth::id())
            ->where('type', BuddyRequestTypeEnum::INVITE)
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->get();
    }

    /**
     * Sent requests.
     *
     * @param int $page
     * @param int $pageSize
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function buddySent(int $page, int $pageSize)
    {
        return BuddyRequest::on()
            ->select(DB::raw('
                id as id,
                user2_id as buddy,
                shoogle_id as shoogle_id,
                created_at as created_at,
                message as message
            '))
            ->where('user1_id', Auth::id())
            ->offset($page * $pageSize - $pageSize)
            ->limit($pageSize)
            ->orderBy('type', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Accept the invitation.
     *
     * @param int $buddyRequestId
     * @throws \Exception
     */
    public function buddyConfirm(int $buddyRequestId): void
    {
        if ( ! HelperBuddyRequest::isActualInvite($buddyRequestId) ) {
            throw new \Exception("The invitation is $buddyRequestId no longer relevant",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::transaction( function () use ($buddyRequestId) {
            $buddyRequest = BuddyRequest::on()
                ->where('id', $buddyRequestId)->first();

            $user1Id = $buddyRequest->user1_id;
            $user2Id = $buddyRequest->user2_id;
            $this->isUsersInCompany($user1Id, $user2Id);

            $buddie = Buddie::on()
                ->where('shoogle_id', $buddyRequest->shoogle_id)
                ->where(function ($query) use ($user1Id, $user2Id) {

                    $query->where(function ($query) use ($user1Id, $user2Id) {
                        $query->where('user1_id', '=', $user1Id)
                            ->where('user2_id', '=', $user2Id);
                    })
                        ->orWhere(function ($query) use ($user1Id, $user2Id) {
                            $query->where('user2_id', '=', $user1Id)
                                ->where('user1_id', '=', $user2Id);
                        });

                })
                ->withoutGlobalScope(BuddiesScope::class)
                ->whereNotNull('disconnected_at')
                ->first();

            if ( ! is_null($buddie) ) {
                $buddie->disconnected_at = null;
            } else {
                $buddie = Buddie::on()->updateOrCreate(
                    [
                        'shoogle_id' => $buddyRequest->shoogle_id,
                        'user1_id' => $buddyRequest->user1_id,
                        'user2_id' => $buddyRequest->user2_id,
                    ],
                    [
                        'connected_at' => Carbon::now(),
                    ]
                );
            }

            $buddyRequest->update([
                'type' => BuddyRequestTypeEnum::CONFIRM,
            ]);

            $userName = HelperUser::getFullName( $buddyRequest->user2_id );
            $shoogleTitle = HelperShoogle::getTitle( $buddyRequest->shoogle_id );
            $messageText = "$userName has accepted your invitation to buddy up in $shoogleTitle.";

            $helperNotification = new HelperNotifications();
            $helperNotification->sendNotificationToUser(
                $buddyRequest->user1_id,
                NotificationsTypeConstant::BUDDY_CONFIRM_ID,
                $messageText
            );
            $helperNotification->recordNotificationDetail(
                $buddyRequest->shoogle_id,
                $buddyRequest->user2_id,
                NotificationTextConstant::BUDDY_CONFIRM,
                $buddyRequest->id,
                $buddie->id
            );

            $shoogle = Shoogle::on()->where('id', $buddyRequest->shoogle_id)->first();
            $streamService = new StreamService($buddyRequest->shoogle_id);
            $channelId = $streamService->createChannelForBuddy($shoogle->title ,$buddyRequest->user1_id, $buddyRequest->user2_id, $shoogle->cover_image);

            $buddie->update([
                'chat_id' => $channelId,
            ]);

            UserHasShoogle::on()
                ->where('user_id', '=', $buddyRequest->user1_id)
                ->where('shoogle_id', '=', $buddyRequest->shoogle_id)
                ->update([
                    'solo' => 0,
                ]);

            HelperNotific::checkMark($buddyRequest->id, NotificationsTypeConstant::BUDDY_REQUEST_ID, true);
        });
    }

    /**
     * Reject friend request.
     *
     * @param \Illuminate\Database\Eloquent\Model|object|static|null $buddyRequest
     * @throws \GetStream\StreamChat\StreamException
     */
    public function buddyReject(BuddyRequest $buddyRequest): void
    {
        DB::transaction( function () use ($buddyRequest) {

            $this->isUsersInCompany($buddyRequest->user1_id, $buddyRequest->user2_id);

            $buddyRequest->update([
                'type' => BuddyRequestTypeEnum::REJECT,
            ]);

            $user2Name = HelperUser::getFullName($buddyRequest->user2_id);
            $shoogleTitle = HelperShoogle::getTitle($buddyRequest->shoogle_id);
            $messageText = "$user2Name has rejected your invitation to buddy up in $shoogleTitle .";

            $helperNotification = new HelperNotifications();
            $helperNotification->sendNotificationToUser(
                $buddyRequest->user1_id,
                NotificationsTypeConstant::BUDDY_REJECT_ID,
                $messageText
            );
            $helperNotification->recordNotificationDetail(
                $buddyRequest->shoogle_id,
                $buddyRequest->user2_id,
                NotificationTextConstant::BUDDY_REJECT,
                $buddyRequest->id
            );

            HelperNotific::checkMark($buddyRequest->id, NotificationsTypeConstant::BUDDY_REQUEST_ID, true);
        });
    }

    /**
     * Leaving friends.
     *
     * @param int $buddyId
     * @param int $shoogleId
     * @param int $callerUserId
     * @param string|null $message
     */
    public function buddyDisconnect(int $buddyId, int $shoogleId, int $callerUserId, ?string $message): void
    {
        DB::transaction( function () use ($buddyId, $shoogleId, $callerUserId, $message) {

            $this->isUsersInCompany($buddyId, $callerUserId);

            $buddyRequestFields = ['type' => BuddyRequestTypeEnum::DISCONNECT];
            if ( ! is_null($message) ) {
                $buddyRequestFields['message'] = $message;
            }

            $buddyRequest = BuddyRequest::on()
                ->where('shoogle_id', $shoogleId)
                ->where(function ($query) use ($buddyId, $callerUserId) {

                    $query->where(function($query) use ($buddyId, $callerUserId) {
                            $query->where('user1_id', $buddyId)
                                ->where('user2_id', $callerUserId);
                        })
                        ->orWhere(function($query) use ($buddyId, $callerUserId) {
                            $query->where('user1_id', $callerUserId)
                                ->where('user2_id', $buddyId);
                        });

                })
                ->orderBy('created_at', 'DESC')
                ->first();

            $buddyRequest->update($buddyRequestFields);

            $buddie = Buddie::on()
                ->whereNull('disconnected_at')
                ->where('shoogle_id', $shoogleId)
                ->where(function ($query) use ($buddyId, $callerUserId) {

                    $query->where(function($query) use ($buddyId, $callerUserId) {
                        $query->where('user1_id', $buddyId)
                            ->where('user2_id', $callerUserId);
                    })
                        ->orWhere(function($query) use ($buddyId, $callerUserId) {
                            $query->where('user1_id', $callerUserId)
                                ->where('user2_id', $buddyId);
                        });

                })
                ->orderBy('created_at', 'DESC')
                ->first();

            $buddie->update([
                'disconnected_at' => Carbon::now(),
            ]);

            $userName = HelperUser::getFullName( $callerUserId );
            $shoogleTitle = HelperShoogle::getTitle( $shoogleId );
            $messageText = "$userName left $shoogleTitle. You are no longer buddied.";

            $helperNotification = new HelperNotifications();
            $helperNotification->sendNotificationToUser($buddyId, NotificationsTypeConstant::BUDDY_DISCONNECT_ID, $messageText);

            $buddyRequestId = ( ! is_null( $buddyRequest ) ) ? $buddyRequest->id : null;
            $buddieId = ( ! is_null( $buddie ) ) ? $buddie->id : null;
            $helperNotification->recordNotificationDetail($shoogleId, $callerUserId, $message, $buddyRequestId, $buddieId);
        });
    }
}
