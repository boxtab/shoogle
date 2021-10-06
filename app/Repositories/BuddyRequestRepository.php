<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\Helper;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperMember;
use App\Models\BuddyRequest;
use App\Models\Company;
use App\Scopes\BuddiesScope;
use App\Services\StreamService;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Buddie;

/**
 * Class BuddyRequestRepository
 * @package App\Repositories
 */
class BuddyRequestRepository extends Repositories
{
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
            throw new \Exception("User id:$user1Id is not a member of shoogle id:$shoogleId",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( ! HelperMember::isMember($shoogleId, $user2Id) ) {
            throw new \Exception("User id:$user2Id is not a member of shoogle id:$shoogleId",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( HelperBuddies::isFriends($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception("Users id:$user1Id and id:$user2Id of Chat id:$shoogleId are already friends",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( HelperBuddyRequest::isBuddyRequest($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception("User id:$user1Id has already sent a friend request to user id:$user2Id for shoogle id:$shoogleId",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        BuddyRequest::create([
            'shoogle_id'    => $shoogleId,
            'user1_id'      => $user1Id,
            'user2_id'      => $user2Id,
            'type'          => BuddyRequestTypeEnum::INVITE,
            'message'       => $message,
        ]);
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

        $buddyRequest = BuddyRequest::on()
            ->where('id', $buddyRequestId)->first();

        $shoogleId = $buddyRequest->shoogle_id;
        $user1Id = $buddyRequest->user1_id;
        $user2Id = $buddyRequest->user2_id;

        if ( HelperBuddies::isFriends($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception("User id:$user1Id and user id:$user2Id are already friends in shoogle id:$shoogleId",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::transaction( function () use ($buddyRequestId) {
            $buddyRequest = BuddyRequest::on()
                ->where('id', $buddyRequestId)->first();

            $user1Id = $buddyRequest->user1_id;
            $user2Id = $buddyRequest->user2_id;
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
                $buddie = Buddie::on()->create([
                    'shoogle_id' => $buddyRequest->shoogle_id,
                    'user1_id' => $buddyRequest->user1_id,
                    'user2_id' => $buddyRequest->user2_id,
                    'connected_at' => Carbon::now(),
                ]);
            }

            $buddyRequest->update([
                'type' => BuddyRequestTypeEnum::CONFIRM,
            ]);

            $streamService = new StreamService($buddyRequest->shoogle_id);
            $channelId = $streamService->createChannelForBuddy($buddyRequest->user1_id, $buddyRequest->user2_id);

            $buddie->update([
                'chat_id' => $channelId,
            ]);
        });
    }

    /**
     * Reject friend request.
     *
     * @param int $buddyRequestId
     */
    public function buddyReject(int $buddyRequestId): void
    {
        BuddyRequest::on()
            ->where('id', $buddyRequestId)
            ->where('type', BuddyRequestTypeEnum::INVITE)
            ->update([
                'type' => BuddyRequestTypeEnum::REJECT,
            ]);
    }

    /**
     * Leaving friends.
     *
     * @param int $buddyId
     * @param int $shoogleId
     * @param string|null $message
     */
    public function buddyDisconnect(int $buddyId, int $shoogleId, ?string $message): void
    {
        DB::transaction( function () use ($buddyId, $shoogleId, $message) {

            $buddyRequestFields = ['type' => BuddyRequestTypeEnum::DISCONNECT];
            if ( ! is_null($message) ) {
                $buddyRequestFields['message'] = $message;
            }

            BuddyRequest::on()
                ->where('type', '<>', BuddyRequestTypeEnum::DISCONNECT)
                ->where('shoogle_id', $shoogleId)
                ->orWhere(function($query) use ($buddyId) {
                    $query->where('user1_id', $buddyId)->where('user2_id', Auth::id());
                })
                ->orWhere(function($query) use ($buddyId) {
                    $query->where('user1_id', Auth::id())->where('user2_id', $buddyId);
                })
                ->update($buddyRequestFields);

            Buddie::on()
                ->whereNull('disconnected_at')
                ->where('shoogle_id', $shoogleId)
                ->orWhere(function($query) use ($buddyId) {
                    $query->where('user1_id', $buddyId)->where('user2_id', Auth::id());
                })
                ->orWhere(function($query) use ($buddyId) {
                    $query->where('user1_id', Auth::id())->where('user2_id', $buddyId);
                })
                ->update([
                    'disconnected_at' => Carbon::now(),
                ]);
        });
    }
}
