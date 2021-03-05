<?php

namespace App\Events;

use App\Tour;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class JunketRejectNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that was registered.
     *
     * @var \App\Tour
     */
    public $tour;
    public $user;
    public $reason;

    /**
     * Create a new event instance.
     *
     * @param mixed $tour
     * @return void
     */
    public function __construct($tour, $user, $reason)
    {
        $this->tour = $tour;
        $this->user = $user;
        $this->reason = $reason;
    }
}
