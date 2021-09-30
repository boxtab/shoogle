<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * Class HelperMember
 * @package App\Helpers
 */
class HelperMember
{
    /**
     * Whether the user is a member of the shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return bool
     */
    public static function isMember(?int $shoogleID, ?int $userID): bool
    {
        if ( is_null( $shoogleID ) || is_null( $userID ) ) {
            return false;
        }

        return UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->exists();
    }

    /**
     * Is the user a member of shoogle.
     *
     * @param int|null $shoogleID
     * @param int|null $userID
     * @return bool
     */
    public static function isMember2(?int $shoogleID, ?int $userID): bool
    {
        if ( is_null( $shoogleID ) || is_null( $userID ) ) {
            return false;
        }

        return UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->exists();
    }

    /**
     * Returns a member of the shoogle or generates an error.
     *
     * @param int|null $userId
     * @param int|null $shoogleId
     * @return UserHasShoogle|\Illuminate\Database\Eloquent\Builder|Model|object
     * @throws \Exception
     */
    public static function getMember(?int $userId, ?int $shoogleId): UserHasShoogle
    {
        $member = UserHasShoogle::on()
            ->where('user_id', '=', $userId)
            ->where('shoogle_id', '=', $shoogleId)
            ->first();

        if ( is_null( $member ) ) {
            $message = "By userId $userId and shoogleId $shoogleId, the participant was not found or left shoogle or was deleted";
            throw new \Exception($message, Response::HTTP_NOT_FOUND);
        }

        return $member;
    }
}
