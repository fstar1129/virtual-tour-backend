<?php

namespace App\Listeners;

use App\Events\UserWasConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserConfirmationMail;

class SendUserConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserWasConfirmed  $event
     * @return void
     */
    public function handle(UserWasConfirmed $event)
    {
        Mail::to($event->user->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(new UserConfirmationMail($event->user));
    }
}
