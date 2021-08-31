<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Enums\BuddyRequestTypeEnum;
use App\Helpers\Helper;
use App\Models\BuddyRequest;
use App\Models\Company;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @param int $user2id
     * @param string|null $message
     */
    public function buddyRequest(int $shoogleId, int $user2id, string $message = null): void
    {
        $buddyRequest = BuddyRequest::on()
            ->where('shoogle_id', $shoogleId)
            ->where('user1_id', Auth::id())
            ->where('user2_id', $user2id)
            ->exists();

        if ( $buddyRequest === true ) {
            return;
        }

        BuddyRequest::create([
            'shoogle_id'    => $shoogleId,
            'user1_id'      => Auth::id(),
            'user2_id'      => $user2id,
            'type'          => BuddyRequestTypeEnum::INVITE,
            'message'       => $message,
        ]);

    }

    /**
     * Accept the invitation.
     *
     * @param int $buddyRequestId
     */
    public function buddyConfirm(int $buddyRequestId)
    {
        BuddyRequest::on()
            ->where('id', $buddyRequestId)
            ->update([
                'type' => BuddyRequestTypeEnum::CONFIRM,
            ]);
    }
}
