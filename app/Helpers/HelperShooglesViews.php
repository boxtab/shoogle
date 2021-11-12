<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\ShoogleViews;
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

        $userID = $shooglesViews->user_id;
        $profileImage = User::on()
            ->where('id', '=', $userID)
            ->first('profile_image')
            ->profile_image;

        $response['id'] = $userID;
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

        return ShoogleViews::on()
            ->whereHas('user')
            ->whereHas('userHasShoogle', function ($query) {
                $query
                    ->whereNull('left_at');
            })
            ->where('shoogles_views.shoogle_id', '=', $shoogleID)
            ->orderBy('shoogles_views.last_view', 'DESC')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->user_id,
                    'avatar' => HelperAvatar::getURLProfileImage($item->user->profile_image),
                ];
            })
            ->toArray();
    }

}
