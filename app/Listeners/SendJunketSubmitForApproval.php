<?php

namespace App\Listeners;

use App\Events\JunketSubmitForApprovalEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\JunketSubmitForApproval;
use App\Mail\JunketSubmitNotification;
use Config;
use App\PricingPlan;

class SendJunketSubmitForApproval implements ShouldQueue
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
    public function handle(JunketSubmitForApprovalEvent $event)
    {
        $pricing_plan = $event->user->getPricingPlan();
        Mail::to($event->user->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(new JunketSubmitForApproval(array("tour_title" => $event->tour->title, "user_name" => $event->user->name)));

        Mail::to("contact@wejunket.com")
        // Mail::to("sandybux99@gmail.com")
            ->send(new JunketSubmitNotification(array(
                "tour_title" => $event->tour->title,
                "user_name" => $event->user->email,
                "tour_location" => $event->tour->location->city == '' ? '' : ($event->tour->location->city . ", ") . $event->tour->location->state,
                "number_of_stops" => $event->tour->getStopsCountAttribute(),
                "plan" => $pricing_plan->plan_name,
                "review_url" => config('app.cms_url') . "/#/tour/" . $event->tour->id,
            )));
    }
}
