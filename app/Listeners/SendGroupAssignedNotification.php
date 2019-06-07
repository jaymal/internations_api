<?php

namespace App\Listeners;

use App\Events\UserAssignedToGroup;
use App\Notifications\GroupAssigned;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendGroupAssignedNotification
{
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
     * @param  UserAssignedToGroup  $event
     * @return void
     */
    public function handle(UserAssignedToGroup $event)
    {
        // send notification using the "Notification" facade
        Notification::send($event->userGroup->user, new GroupAssigned($event->userGroup));
    }
}
