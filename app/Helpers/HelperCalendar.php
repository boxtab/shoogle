<?php

namespace App\Helpers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class HelperCalendar
 * @package App\Helpers
 */
class HelperCalendar
{
    /**
     * Returns a friend.
     *
     * @param int|null $friendID
     * @return array|null
     */
    public static function getBuddy(?int $friendID): ?array
    {
        $friend = User::on()->where('id', '=', $friendID)->get('profile_image');

        if ( ! $friend->isNotEmpty() ) {
            return null;
        }

        return [
            'id' => $friendID,
            'profileImage' => HelperAvatar::getURLProfileImage( $friend[0]->profile_image ),
        ];
    }
}
