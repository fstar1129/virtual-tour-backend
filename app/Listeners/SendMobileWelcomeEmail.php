<?php

namespace App\Listeners;

use App\Events\MobileUserWasRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\MobileWelcomeMail;

class SendMobileWelcomeEmail implements ShouldQueue
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
     * @param  MobileUserWasRegistered  $event
     * @return void
     */
    public function handle(MobileUserWasRegistered $event)
    {
        Mail::to($event->user->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(new MobileWelcomeMail($event->user));
    }
}
