<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperMember
 * @package App\Helpers
 */
class HelperMember
{
    /**
     * Whether the user is a member of the shoogle.
     *
     * @param int|null $shoogleId
     * @param int|null $userId
     * @return bool
     */
    public static function isMember(?int $shoogleId, ?int $userId): bool
    {
        if ( is_null( $shoogleId ) || is_null( $userId ) ) {
            return false;
        }

        $countUserHasShoogle = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->where('user_id', '=', $userId)
            ->count();

        return ( $countUserHasShoogle > 0 ) ? true : false;
    }

    /**
     * Get a list of shoogle member ids.
     *
     * @param int|null $shoogleId
     * @return array
     */
    public static function getListMemberIDs(?int $shoogleId): array
    {
        if ( is_null($shoogleId) ) {
            return [];
        }

        return UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->get('user_id')
            ->map(function ($item) {
                return $item['user_id'];
            })
            ->toArray();
    }

    /**
     * Returns a member of the shoogle or generates an error.
     *
     * @param int|null $userId
     * @param int|null $shoogleId
     * @return UserHasShoogle|\Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getMember(?int $userId, ?int $shoogleId)
    {
        return UserHasShoogle::on()
            ->where('user_id', '=', $userId)
            ->where('shoogle_id', '=', $shoogleId)
            ->first();
    }
}
