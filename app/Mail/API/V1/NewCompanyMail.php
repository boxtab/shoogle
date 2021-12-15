<?php

namespace App\Mail\API\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewCompanyMail
 * @package App\Mail\API\V1
 */
class NewCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = config('mail.new_company.email_from'), $name = 'Shoogle')
            ->subject( config('mail.new_company.subject') )
            ->view('emails.new-company');
    }
}
