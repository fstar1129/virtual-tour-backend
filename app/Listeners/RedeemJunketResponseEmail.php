<?php

namespace App\Listeners;

use App\Events\RedeemJunketEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RedeemJunketEmail;
use Config;

class RedeemJunketResponseEmail implements ShouldQueue
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
     * @param  RedeemJunketEvent  $event
     * @return void
     */
    public function handle(RedeemJunketEvent $event)
    {
        Mail::to($event->email)
        // Mail::to("sandybux99@gmail.com")
            ->send(
                new RedeemJunketEmail(
                    array(
                        "tour_id" => $event->tour->id,
                        "tour_image" => $event->tour->mainImage->getPathAttribute(),
                        "tour_title" => $event->tour->title,
                        "tour_description" => $event->tour->description,
                        "tour_location" => $event->tour->location->city . ", " . $event->tour->location->state,
                        "number_of_stops" => $event->tour->getStopsCountAttribute(),
                        "total_audio" => $event->tour->getTotalAudioLength(),
                        "promo_codes" => $event->promo_codes,
                        "user_name" => $event->first_name . ($event->last_name == '' ? '' : (' ' . $event->last_name))
                    )
                )
            );
    }
}
