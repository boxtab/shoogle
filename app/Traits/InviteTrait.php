<?php

namespace App\Traits;

use App\Mail\API\V1\InviteMail;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * Trait InviteTrait
 * @package App\Traits
 */
trait InviteTrait
{
    use CheckEmailSettingsTrait;

    /**
     * Send invitations to email.
     *
     * @param string $email
     * @throws Exception
     */
    private function sendInvitationsToEmail(string $email)
    {
        $this->checkBasicEmailSettings();
        $this->checkingEmailAddress($email);
        $this->checkEmailHeaders('invite');

        try {
            $inviteMail = new InviteMail();
            $inviteMail->to( $email );
            Mail::send( $inviteMail );
        } catch (Exception $e) {
            $error = $e->getMessage();
            throw new Exception("EMAIL NOT SENT! $error", Response::HTTP_BAD_GATEWAY);
        }
    }

}
