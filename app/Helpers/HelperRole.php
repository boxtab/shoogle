<?php

namespace App\Helpers;

use App\User;

/**
 * Class HelperRole
 * @package App\Helpers
 */
class HelperRole
{
    /**
     * Get a role by email.
     *
     * @param string $email
     * @return string|null
     */
    public static function getRoleByEmail(string $email): ?string
    {
        $user = User::on()
            ->where('email', '=', $email)
            ->first();

        if (is_null($user)) {
            return null;
        }

        $roleCollection = $user->getRoleNames();

        if ( count($roleCollection) < 1 ) {
            return null;
        }

        return $roleCollection[0];
    }
}
