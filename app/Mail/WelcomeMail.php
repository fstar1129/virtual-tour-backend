<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user that has just joined.
     *
     * @var \App\User
     */
    public $user;

    /**
     * The action URL.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @param mixed $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->url = config('app.support_url') . "/#/confirm-email?token={$user->email_confirmation_token}";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Junket!')
            ->markdown('mail.welcome');
    }
}
