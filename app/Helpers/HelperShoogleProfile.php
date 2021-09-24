<?php

namespace App\Helpers;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperShoogleProfile
 * @package App\Helpers
 */
class HelperShoogleProfile
{
    /**
     * Get all shoogles a user participates in.
     *
     * @param int|null $userID
     * @return array
     */
    public static function getShooglesByUserID(?int $userID)
    {
        if ( is_null( $userID ) ) {
            return [];
        }

        $shooglesIDs = HelperShoogle::getShooglesIDsByUserID($userID);

        $shoogles = Shoogle::on()
            ->select(DB::raw("
                shoogles.id as id,
                shoogles.title as title,
                shoogles.cover_image as coverImage,
                if((exists (
                    select * from buddies as b
                    where b.shoogle_id = shoogles.id
                      and (
                        b.user1_id = $userID or b.user2_id = $userID
                      )
                )), true, false) as baddies,
                if((exists (
                    select * from user_has_shoogle as uhs
                    where uhs.solo = 1
                      and uhs.shoogle_id = shoogles.id
                      and uhs.user_id = $userID
                )), true, false) as solo
            "))
            ->whereIn('id', $shooglesIDs)
            ->get()
            ->map(function ($item) {
                $item['baddies'] = (bool)$item['baddies'];
                $item['solo'] = (bool)$item['solo'];
                return $item;
            })
            ->toArray();

        return $shoogles;
    }
}
