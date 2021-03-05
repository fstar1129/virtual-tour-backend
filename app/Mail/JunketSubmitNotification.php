<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class JunketSubmitNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $tour_details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->tour_details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("noreply@wejunket.com")
                    ->subject("A user has submitted a Junket!")
                    ->markdown('mail.junket-submit-notification')
                    ->with('tour_details', $this->tour_details);
    }
}
