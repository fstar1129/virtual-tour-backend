<?php

namespace App\Events;

use App\Tour;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class JunketApproveNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that was registered.
     *
     * @var \App\Tour
     */
    public $tour;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param mixed $tour
     * @return void
     */
    public function __construct($tour, $user)
    {
        $this->tour = $tour;
        $this->user = $user;
    }
}
