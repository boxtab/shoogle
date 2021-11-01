<?php

namespace App\Services;

use App\User;

/**
 * Class PasswordRecoveryService
 * @package App\Services
 */
class PasswordRecoveryService
{
    /**
     * Returns a password recovery code.
     *
     * @param string $email
     * @return int
     */
    public function getCode(string $email): int
    {
        do {
            $code = mt_rand(10000, 99999);
            $hashCode = bcrypt($code);
            $user = User::on()
                ->where('password_recovery_code', '=', $hashCode)
                ->first();
        } while ( ! is_null($user) );

        User::on()
            ->where('email', '=', $email)
            ->update([
                'password_recovery_code' => $hashCode
            ]);

        return $code;
    }
}
