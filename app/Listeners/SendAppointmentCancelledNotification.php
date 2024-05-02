<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AppointmentCancelled;

class SendAppointmentCancelledNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentCancelled $event)
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;

        // 2. Send Push Notification (Example using Pusher)
        Pusher::trigger('appointment-channel', 'appointment-cancelled', [
            'message' => 'Your appointment has been cancelled by the medstaff.'
        ]);
    }
}
