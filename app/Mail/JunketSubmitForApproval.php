<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class JunketSubmitForApproval extends Mailable
{
    use Queueable, SerializesModels;
    public $tour_details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tour_details)
    {
        $this->tour_details = $tour_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("noreply@wejunket.com")
                    ->markdown('mail.junket-submit-for-approval')
                    ->subject("Your Junket has been submitted!")
                    ->with('tour_details', $this->tour_details);
    }
}
