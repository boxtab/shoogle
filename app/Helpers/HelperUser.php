<?php

namespace App\Helpers;

use App\User;

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
            ->first(['first_name', 'last_name']);

        if ( is_null( $user ) ) {
            return 'The user with the given ID was not found.';
        }

        return $user->first_name . ' ' . $user->last_name;
    }
}
