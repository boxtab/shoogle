<?php

namespace App\Mail\API\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMobileMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Recovery code.
     *
     * @var int
     */
    private $code;

    /**
     * ResetPasswordMobileMail constructor.
     * @param int $code
     */
    public function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( config('mail.password-recovery.email_from') )
            ->subject( config('mail.password-recovery.subject') )
            ->view('emails.reset-password-mobile')
            ->with('code', $this->code);
    }
}
