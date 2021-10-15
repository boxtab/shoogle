<?php

namespace App\Traits;

use App\Constants\EnvConstant;
use Exception;
use Illuminate\Http\Response;

/**
 * Trait CheckEmailSettingsTrait
 * @package App\Traits
 */
trait CheckEmailSettingsTrait
{
    /**
     * Checking basic email settings.
     *
     * @throws Exception
     */
    private function checkBasicEmailSettings()
    {
        $credentials = false;
        $countVarEnv = 0;

        while ($countVarEnv < count(EnvConstant::$emailInvite)) {

            if ( is_null( config( 'mail.email.' . EnvConstant::$emailInvite[$countVarEnv] ) ) ) {
                $credentials = EnvConstant::$emailInvite[$countVarEnv];
                break;
            }
            $countVarEnv++;

        }

        if ( $credentials !== false ) {
            throw new Exception("$credentials variable not found in environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Checking the email address.
     *
     * @param string|null $email
     * @throws Exception
     */
    private function checkingEmailAddress(?string $email)
    {
        if ( empty( $email ) ) {
            throw new Exception('Email address is empty!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Checking email headers.
     *
     * @param string $section
     * @throws Exception
     */
    private function checkEmailHeaders(string $section)
    {
        if ( empty( config("mail.$section.email_from") ) ) {
            throw new Exception("The environment file does not specify from whom to send mail!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ( empty( config("mail.$section.subject") ) ) {
            throw new Exception("The email subject is not specified in the environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

