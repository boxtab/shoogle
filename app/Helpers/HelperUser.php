<?php

namespace App\Helpers;

use App\Services\UserDeleteService;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperUser
 * @package App\Helpers
 */
class HelperUser
{
    /**
     * Get full username by id.
     *
     * @param int|null $userId
     * @return string
     */
    public static function getFullName(?int $userId): string
    {
        if ( is_null( $userId ) ) {
            return 'No username because no identifier passed.';
        }

        $user = User::on()
            ->where('id', '=', $userId)
            ->first();

        if ( is_null( $user ) ) {
            return 'The user with the given ID was not found.';
        }

        return $user->first_name . ' ' . $user->last_name;
    }

    /**
     * Deleting a user and all associated data.
     *
     * @param int|null $userId
     */
    public static function delete(?int $userId)
    {
        if ( is_null($userId) ) {
            return;
        }

        DB::transaction(function () use ($userId) {
            $userDeleteService = new UserDeleteService($userId);
            $userDeleteService->buddyReject();
            $userDeleteService->buddyDisconnect();
            $userDeleteService->deleteUserHasShoogle();
            $userDeleteService->deleteShoogleViews();
            $userDeleteService->deleteUserHasReward();
            $userDeleteService->deleteInvite();
            $userDeleteService->deleteUser();
        });
    }
}
