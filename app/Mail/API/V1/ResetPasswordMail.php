<?php

namespace App\Mail\API\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Password reset link.
     *
     * @var string
     */
    private $link;

    /**
     * Create a new message instance.
     * @param string $link
     * @return void
     */
    public function __construct(string $link)
    {
        $this->link = $link;
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
            ->view('emails.reset-password')
            ->with('link', $this->link);

//        return $this->view('view.name');
    }
}
