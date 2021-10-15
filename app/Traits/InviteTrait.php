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
    /**
     * Send invitations to email.
     *
     * @param string $email
     * @throws Exception
     */
    private function sendInvitationsToEmail(string $email)
    {
        if ( empty( $email ) ) {
            throw new Exception('Email list to send is empty!', Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $credentialsEmail = $this->isCredentialsEmail();
        if ( $credentialsEmail !== false ) {
            throw new Exception("$credentialsEmail variable not found in environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ( empty( config('mail.invite.email_from') ) ) {
            throw new Exception("The environment file does not specify from whom to send mail!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ( empty( config('mail.invite.subject') ) ) {
            throw new Exception("The email subject is not specified in the environment file!", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
