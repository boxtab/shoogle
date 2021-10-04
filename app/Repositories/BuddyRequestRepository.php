<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\Helper;
use App\Helpers\HelperBuddies;
use App\Helpers\HelperBuddyRequest;
use App\Models\BuddyRequest;
use App\Models\Company;
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
        if ( HelperBuddies::areFriends($shoogleId, $user1Id, $user2Id) ) {
            throw new \Exception("Users $user1Id and $user2Id of Chat 123 are already friends", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ( HelperBuddyRequest::areBuddyRequest($shoogleId, $user1Id, $user2Id) ) {
            Log::info('enter2');
            return;
        }

        $buddyRequest = BuddyRequest::on()
            ->where('shoogle_id', $shoogleId)
            ->where('user1_id', $user1Id)
            ->where('user2_id', $user2Id)
            ->exists();

        if ( $buddyRequest === true ) {
            return;
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
     */
    public function buddyConfirm(int $buddyRequestId): void
    {
        DB::transaction( function () use ($buddyRequestId) {
            $buddyRequest = BuddyRequest::on()
                ->where('id', $buddyRequestId)->first();

            $buddie = Buddie::on()
                ->where('shoogle_id', $buddyRequest->shoogle_id)
                ->where('user1_id', $buddyRequest->user1_id)
                ->where('user2_id', $buddyRequest->user2_id)
                ->firstOr(function() use($buddyRequest) {
                    Buddie::on()->create([
                        'shoogle_id' => $buddyRequest->shoogle_id,
                        'user1_id' => $buddyRequest->user1_id,
                        'user2_id' => $buddyRequest->user2_id,
                        'connected_at' => Carbon::now(),
                    ]);
                });

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
