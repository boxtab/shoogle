<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Basic email distribution settings
    |--------------------------------------------------------------------------
    | Please edit these parameters in the environment file.
    |
    */

    'email' => [
        'mail_mailer' => env('MAIL_MAILER'),
        'mail_host' => env('MAIL_HOST'),
        'mail_port' => env('MAIL_PORT'),
        'mail_username' => env('MAIL_USERNAME'),
        'mail_password' => env('MAIL_PASSWORD'),
        'mail_encryption' => env('MAIL_ENCRYPTION'),
        'mail_from_address' => env('MAIL_FROM_ADDRESS'),
        'mail_from_name' => env('MAIL_FROM_NAME'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Addresses for sending invitations
    |--------------------------------------------------------------------------
    | Please edit these parameters in the environment file.
    |
    */

    'invite' => [
        'email_from' => env('INVITE_EMAIL_FROM', 'support@shoogle.com'),
        'subject' => env('INVITE_SUBJECT', 'We invite you to the system shoogle.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password recovery address
    |--------------------------------------------------------------------------
    | Please edit these parameters in the environment file.
    |
    */

    'password-recovery' => [
        'email_from' => env('PASSWORD_RECOVERY_EMAIL_FROM', 'support@shoogle.com'),
        'subject' => env('PASSWORD_RECOVERY_SUBJECT', 'Password recovery'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Send an invitation to a new company.
    |--------------------------------------------------------------------------
    | Please edit these parameters in the environment file.
    |
    */

    'new_company' => [
        'email_from' => env('NEW_COMPANY_EMAIL_FROM', 'support@shoogle.com'),
        'subject' => env('NEW_COMPANY_SUBJECT', 'You were invited to a new company.'),
    ],

];
