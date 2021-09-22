<?php

namespace App\Helpers;

use App\Models\Shoogle;

/**
 * Class HelperShoogleProfile
 * @package App\Helpers
 */
class HelperShoogleProfile
{
    /**
     * Returns the shoogle from which there was a transition.
     *
     * @param int|null $shoogleID
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getFollowing(?int $shoogleID)
    {
        if ( is_null( $shoogleID ) ) {
            return null;
        }

        return Shoogle::on()->where('id', '=', $shoogleID)->first('title');
    }

    public static function getOther(?int $userID)
    {
        if ( is_null( $userID ) ) {
            return null;
        }


    }
}
