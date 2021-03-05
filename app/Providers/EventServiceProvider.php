<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\TourJoined;
use App\Listeners\MarkTourDownloadedListener;
use App\Events\UserWasRegistered;
use App\Listeners\SendWelcomeEmail;
use App\Events\MobileUserWasRegistered;
use App\Listeners\SendMobileWelcomeEmail;
use App\Events\ChangeEmailRequestCreated;
use App\Listeners\SendChangeEmailActivation;
use App\Events\JunketSubmitForApprovalEvent;
use App\Listeners\SendJunketSubmitForApproval;
use App\Events\JunketApproveNotificationEvent;
use App\Listeners\SendJunketApproveNotification;
use App\Events\JunketRejectNotificationEvent;
use App\Listeners\SendJunketRejectNotification;
use App\Events\UserWasConfirmed;
use App\Listeners\SendUserConfirmationEmail;
use App\Events\RedeemJunketEvent;
use App\Listeners\RedeemJunketResponseEmail;
use App\Events\ConfirmationChangeOfPlan;
use App\Listeners\ConfirmationChangeOfPlanListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        TourJoined::class => [
            MarkTourDownloadedListener::class,
        ],
        UserWasRegistered::class => [
            SendWelcomeEmail::class,
        ],
        MobileUserWasRegistered::class => [
            SendMobileWelcomeEmail::class,
        ],
        ChangeEmailRequestCreated::class => [
            SendChangeEmailActivation::class,
        ],
        JunketSubmitForApprovalEvent::class => [
            SendJunketSubmitForApproval::class,
        ],
        JunketApproveNotificationEvent::class => [
            SendJunketApproveNotification::class,
        ],
        JunketRejectNotificationEvent::class => [
            SendJunketRejectNotification::class,
        ],
        UserWasConfirmed::class => [
            SendUserConfirmationEmail::class,
        ],
        RedeemJunketEvent::class => [
            RedeemJunketResponseEmail::class,
        ],
        ConfirmationChangeOfPlan::class => [
            ConfirmationChangeOfPlanListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
