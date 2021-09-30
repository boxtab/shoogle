<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;

/**
 * Class HelperShoogle
 * @package App\Helpers
 */
class HelperShoogle
{
    /**
     * Get all id shoogles by user id.
     *
     * @param int|null $userID
     * @return array
     */
    public static function getShooglesIDsByUserID(?int $userID): array
    {
        if ( is_null( $userID ) ) {
            return [];
        }

        $shoogleIDsFromOwner = Shoogle::on()
            ->where('owner_id', '=', $userID)
            ->get('id')
            ->map(function ($item) {
                return $item['id'];
            })
            ->toArray();

        $shooglesIDsFromMembers = UserHasShoogle::on()
            ->where('user_id', '=', $userID)
            ->get('shoogle_id')
            ->map(function ($item) {
                return $item['shoogle_id'];
            })
            ->toArray();

        return array_unique( array_merge($shoogleIDsFromOwner, $shooglesIDsFromMembers) );
    }

    /**
     * Is a user a member of shoogle.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isMember(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        $ownerCount = Shoogle::on()
            ->where('id' , '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->count();
        $isOwner = ($ownerCount > 0) ? 1 : 0;


        $userHasShoogleCount = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->where('user_id', '=', $userID)
            ->whereNull('left_at')
            ->exists();
        $isUserHasShoogle = ($userHasShoogleCount > 0) ? 1 : 0;

        return ( $isOwner || $isUserHasShoogle ) ? true : false;
    }

    /**
     * Is the user the owner of a shoogle.
     *
     * @param int|null $userID
     * @param int|null $shoogleID
     * @return bool
     */
    public static function isOwner(?int $userID, ?int $shoogleID): bool
    {
        if ( is_null($userID) || is_null($shoogleID) ) {
            return false;
        }

        return Shoogle::on()
            ->where('id' , '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->exists();
    }

    /**
     * Return shoogle by identifier or generate an error.
     *
     * @param int|null $shoogleId
     * @return Shoogle|\Illuminate\Database\Eloquent\Builder|Model|object
     * @throws \Exception
     */
    public static function getShoogle(?int $shoogleId): Shoogle
    {
        $shoogle = Shoogle::on()->where('id', '=', $shoogleId)->first();

        if ( is_null( $shoogle ) ) {
            throw new \Exception("Shoogle by ID $shoogleId not found or deleted", Response::HTTP_NOT_FOUND);
        }

        return $shoogle;
    }
}
