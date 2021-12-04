<?php

namespace App\Helpers;

use App\Services\UserDeleteService;
use App\User;
use Exception;
use Illuminate\Http\Response;
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
     * Is the user deleted.
     *
     * @param string|null $email
     * @return bool
     */
    private static function isUserDeleted(?string $email): bool
    {
        if ( is_null($email) ) {
            return true;
        }

        $user = User::withTrashed()->where('email', '=', $email)->first();
        if ( is_null($user) ) {
            return true;
        }

        return ( is_null($user->deleted_at) ) ? false : true;
    }

    /**
     * Checking if the user has been deleted.
     *
     * @param string|null $email
     * @throws Exception
     */
    public static function checkUserDeleted(?string $email)
    {
        if ( self::isUserDeleted($email) ) {
            throw new Exception('Your account has been deleted.', Response::HTTP_NOT_FOUND);
        }
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
            $userDeleteService->pushAccessDenied();
            $userDeleteService->buddyReject();
            $userDeleteService->buddyDisconnect();
            $userDeleteService->deleteUserHasShoogle();
//            $userDeleteService->deleteShoogleViews();
            $userDeleteService->deleteUserHasReward();
            $userDeleteService->deleteInvite();
            $userDeleteService->deleteUser();
        });
    }
}
