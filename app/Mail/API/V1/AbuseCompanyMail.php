<?php

namespace App\Mail\API\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbuseCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Date and time of complaint.
     *
     * @var string
     */
    private $dateAbuseTextFormat;

    /**
     * Who did the complaint come from.
     *
     * @var string
     */
    private $fromUserName;

    /**
     * Who complained.
     *
     * @var string
     */
    private $toUserName;

    /**
     * Company administrator name.
     *
     * @var string
     */
    private $companyAdminName;

    /**
     * AbuseCompanyMail constructor.
     *
     * @param string $dateAbuseTextFormat
     * @param string $fromUserName
     * @param string $toUserName
     * @param string $companyAdminName
     */
    public function __construct($dateAbuseTextFormat, $fromUserName, $toUserName, $companyAdminName)
    {
        $this->dateAbuseTextFormat = $dateAbuseTextFormat;
        $this->fromUserName = $fromUserName;
        $this->toUserName = $toUserName;
        $this->companyAdminName = $companyAdminName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = config('mail.email.mail_from_address'), $name = 'Shoogle')
            ->subject( config('mail.abuse_company.subject') )
            ->view('emails.abuse-company')
            ->with([
                'dateAbuseTextFormat' => $this->dateAbuseTextFormat,
                'fromUserName' => $this->fromUserName,
                'toUserName' => $this->toUserName,
                'companyAdminName' => $this->companyAdminName,
            ]);
    }
}
