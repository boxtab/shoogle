<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use App\Scopes\ShoogleScope;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShoogleList
 * @package App\Helpers
 */
class HelperShoogleList
{
    /**
     * true - If the user is the creator of shoogle.
     * false - else.
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
            ->where('id', '=', $shoogleID)
            ->where('owner_id', '=', $userID)
            ->exists();
    }

    /**
     * Return a list of shoogles in which the user is a member.
     *
     * @param int|null $userId
     * @param bool $isActiveOnly
     * @return array|null
     */
    public static function getList(?int $userId, bool $isActiveOnly = true): ?array
    {
        if ( is_null($userId) ) {
            return null;
        }

        $user = User::on()->where('id', '=', $userId)->get();
        if ( is_null($user) ) {
            return null;
        }

        $listShoogleIds = UserHasShoogle::on()
            ->where('user_id', '=', $userId)
            ->get()
            ->map(function ($item) {
                return $item->shoogle_id;
            })
            ->toArray();

        if ( empty($listShoogleIds) ) {
            return null;
        }

        return Shoogle::on()
            ->whereIn('id', $listShoogleIds )
            ->when( ! $isActiveOnly, function ($query) {
                $query->withoutGlobalScope(ShoogleScope::class);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'wellbeingCategory' => $item->wellbeingCategory->name,
//                    'shooglersCount' => HelperShoogle::getShooglersCount($item->id),
                    'shooglersCount' => UserHasShoogle::on()->where('shoogle_id', $item->id)->count(),
                ];
            })
            ->toArray();
    }
}
