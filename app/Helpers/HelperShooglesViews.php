<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use App\Scopes\UserHasShoogleScope;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShooglesViews
 * @package App\Helpers
 */
class HelperShooglesViews
{
    /**
     * Returns the last active user.
     *
     * @param int|null $shoogleID
     * @return array
     */
    public static function getLastActivityBy(?int $shoogleID): array
    {
        $response = ['id' => null, 'avatar' => null];

        if ( is_null($shoogleID) ) {
            return $response;
        }

        if ( ! Shoogle::on()->where('id', '=', $shoogleID)->exists() ) {
            return $response;
        }

        $shooglesViews = ShoogleViews::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->latest('created_at')
            ->first();

        if ( is_null( $shooglesViews ) ) {
            return $response;
        }

        $userId = $shooglesViews->user_id;
        $user = User::on()
            ->where('id', '=', $userId)
            ->first('profile_image');

        $profileImage = is_null($user) ? null : $user->profile_image;

        $response['id'] = $userId;
        $response['avatar'] = HelperAvatar::getURLProfileImage($profileImage);

        return $response;
    }

    /**
     * Get the latest active shoogla users.
     *
     * @param int|null $shoogleID
     * @return array
     */
    public static function getMostActiveShooglers(?int $shoogleID): array
    {
        $response = [];

        if ( is_null($shoogleID) ) {
            return $response;
        }

        if ( ! Shoogle::on()->where('id', '=', $shoogleID)->exists() ) {
            return $response;
        }

        $userIds = UserHasShoogle::on()
            ->where('shoogle_id', '=', $shoogleID)
            ->get()
            ->map(function ($item) {
                return $item->user_id;
            })
            ->toArray();

        $shoogleViewsModel = ShoogleViews::on()
            ->whereIn('shoogles_views.user_id', $userIds)
            ->where('shoogles_views.shoogle_id', '=', $shoogleID)
            ->orderBy('shoogles_views.last_view', 'DESC');

        $shoogleViews = $shoogleViewsModel
            ->get()
            ->map(function($item) {

                $user = User::on()->where('id', '=', $item->user_id)->first();
                if ( ! is_null($user) ) {
                    $avatar = HelperAvatar::getURLProfileImage($user->profile_image);
                } else {
                    $avatar = null;
                }

                return [
                    'id' => $item->user_id,
                    'avatar' => $avatar,
                ];
            })
            ->toArray();

        return $shoogleViews;
    }

    /**
     * Number of views in shoogle from all users.
     *
     * @param int|null $shoogleId
     * @return int
     */
    public static function getQuantityViews(?int $shoogleId): int
    {
        if ( is_null($shoogleId) ) {
            return 0;
        }

        return ShoogleViews::on()
            ->where('shoogle_id', '=', $shoogleId)
            ->sum('views');
    }
}
