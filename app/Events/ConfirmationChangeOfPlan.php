<?php

namespace App\Events;

use App\Tour;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ConfirmationChangeOfPlan
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that was registered.
     *
     * @var \App\Tour
     */
    public $pricing_plan;
    public $user;
    public $count_additional_junkets;

    /**
     * Create a new event instance.
     *
     * @param mixed $tour
     * @return void
     */
    public function __construct($user, $pricing_plan, $count_additional_junkets)
    {
        $this->pricing_plan = $pricing_plan;
        $this->user = $user;
        $this->count_additional_junkets = $count_additional_junkets;
    }
}
