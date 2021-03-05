<?php

namespace App\Events;

use App\Tour;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RedeemJunketEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that was registered.
     *
     * @var \App\Tour
     */
    public $tour;
    public $email;
    public $first_name;
    public $last_name;
    public $promo_codes;

    /**
     * Create a new event instance.
     *
     * @param mixed $tour
     * @return void
     */
    public function __construct($tour, $email, $first_name, $last_name, $promo_codes)
    {
        $this->tour = $tour;
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->promo_codes = $promo_codes;
    }
}
