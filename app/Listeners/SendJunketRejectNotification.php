<?php

namespace App\Listeners;

use App\Events\JunketRejectNotificationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\JunketRejectNotification;

class SendJunketRejectNotification implements ShouldQueue
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
     * @param  JunketSubmitForApprovalEvent  $event
     * @return void
     */
    public function handle(JunketRejectNotificationEvent $event)
    {
        Mail::to($event->user->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(new JunketRejectNotification(array("tour_title" => $event->tour->title, "user_name" => $event->user->name, "reason" => $event->reason)));
    }
}
