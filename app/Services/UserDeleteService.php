<?php

namespace App\Services;

use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\HelperAccessDenied;
use App\Models\Buddie;
use App\Models\BuddyRequest;
use App\Models\Invite;
use App\Models\ShoogleViews;
use App\Models\UserHasReward;
use App\Models\UserHasShoogle;
use App\Repositories\BuddyRequestRepository;
use App\User;
use Carbon\Carbon;

/**
 * Class UserDeleteService
 * @package App\Services
 */
class UserDeleteService
{
    /**
     * @var int User ID.
     */
    private $userId;

    /**
     * UserDeleteService constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Send notification that access is denied.
     */
    public function pushAccessDenied()
    {
        HelperAccessDenied::pushNotification($this->userId);
    }

    /**
     * Reject all friend requests.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function buddyReject()
    {
        $buddyRequestIds = BuddyRequest::on()
            ->where('user2_id', '=', $this->userId)
            ->where('type', '=', BuddyRequestTypeEnum::INVITE)
            ->get()
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        $modelBuddyRequest = new BuddyRequest;
        $repositoryBuddyRequest = new BuddyRequestRepository($modelBuddyRequest);

        foreach ($buddyRequestIds as $buddyRequestId) {

            $buddyRequest = BuddyRequest::on()
                ->where('id', '=', $buddyRequestId)
                ->first();

            $repositoryBuddyRequest->buddyReject($buddyRequest);
        }
    }

    /**
     * End friendship with all users.
     */
    public function buddyDisconnect()
    {
        $userId = $this->userId;
        $buddies = Buddie::on()
            ->where(function ($query) use ($userId) {
                $query->where('user1_id', '=', $userId)
                    ->orWhere('user2_id', '=', $userId);
            })
            ->whereNull('disconnected_at')
            ->get()
            ->toArray();

        $modelBuddyRequest = new BuddyRequest;
        $repositoryBuddyRequest = new BuddyRequestRepository($modelBuddyRequest);

        foreach ($buddies as $buddy) {
            $buddyId = $buddy['user1_id'] == $this->userId ? $buddy['user2_id'] : $buddy['user1_id'];
            $repositoryBuddyRequest->buddyDisconnect($buddyId, $buddy['shoogle_id'], $userId, null);
        }
    }

    /**
     * Removing user participation in shoogle.
     */
    public function deleteUserHasShoogle()
    {
        UserHasShoogle::on()->where('user_id', '=', $this->userId)->update(['left_at' => Carbon::now()]);
    }

    /**
     * Removes shoogle views.
     */
    public function deleteShoogleViews()
    {
        ShoogleViews::on()->where('user_id', '=', $this->userId)->delete();
    }

    /**
     * Removes awards received by the user.
     */
    public function deleteUserHasReward()
    {
        UserHasReward::on()->where('user_id', '=', $this->userId)->delete();
    }

    /**
     * Removing user invites.
     */
    public function deleteInvite()
    {
        Invite::on()->where('user_id', '=', $this->userId)->delete();
    }

    /**
     * Removing a user by ID.
     */
    public function deleteUser()
    {
        User::on()->where('id', '=', $this->userId)->delete();
    }
}
