<?php

namespace App\Traits;

use App\Mail\API\V1\InviteMail;
use App\Mail\API\V1\NewCompanyMail;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * Trait CompanyTrait
 * @package App\Traits
 */
trait CompanyTrait
{
    use CheckEmailSettingsTrait;

    /**
     * Send an invitation to a new company.
     *
     * @param string|null $email
     * @throws Exception
     */
    private function sendInvitationToNewCompany(?string $email)
    {
        try {
            $this->checkBasicEmailSettings();
            $this->checkingEmailAddress($email);
            $this->checkEmailHeaders('new_company');

            $newCompanyMail = new NewCompanyMail();
            $newCompanyMail->to( $email );
            Mail::send( $newCompanyMail );
        } catch (Exception $e) {
            $error = $e->getMessage();
            throw new Exception("EMAIL NOT SENT! $error", Response::HTTP_BAD_GATEWAY);
        }
    }
}
