<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Events\UserAssignedToGroup;
use App\Listeners\SendUserCreatedNotification;
use App\Listeners\SendGroupAssignedNotification;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        UserCreated::class => [
            SendUserCreatedNotification::class,
        ],
        UserAssignedToGroup::class => [
            SendGroupAssignedNotification::class,
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
