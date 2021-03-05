<?php

namespace App\Listeners;

use App\Events\RedeemJunketEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationChangeOfPlanEmail;
use App\Events\ConfirmationChangeOfPlan;

class ConfirmationChangeOfPlanListener implements ShouldQueue
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
     * @param  ConfirmationChangeOfPlan  $event
     * @return void
     */
    public function handle(ConfirmationChangeOfPlan $event)
    {
        Mail::to($event->user->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(new ConfirmationChangeOfPlanEmail(array(
                "pricing_plan" => $event->pricing_plan,
                "count_additional_junkets" => $event->count_additional_junkets,
                )));
    }
}
