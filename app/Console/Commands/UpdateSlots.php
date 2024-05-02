<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Slot;
use App\Models\BookingLimit;
use App\Models\Appointment;
use Carbon\Carbon;

class UpdateSlots extends Command
{
    protected $signature = 'slots:update';
    protected $description = 'Update slots status based on current date and availability';

    public function handle()
    {
        $currentTime = Carbon::now();
    
        $slots = Slot::all();
    
        foreach ($slots as $slot) {
            $bookingLimit = BookingLimit::find($slot->booking_limit_id);
            if ($bookingLimit && $bookingLimit->slot_number === 0) {
                $slot->status = 'fully booked';
                $slot->save();
                $statusUpdated = true;
            }
        }

        $bookingLimits = BookingLimit::where('date', '<', $currentTime)->get();
        foreach ($bookingLimits as $bookingLimit) {
            $slots = Slot::where('booking_limit_id', $bookingLimit->id)->get();
            foreach ($slots as $slot) {
                if ($slot->status != 'fully booked') {
                    if ($currentTime > Carbon::parse($slot->end_time)) {
                        $slot->status = 'not applicable';
                        $slot->save();
                        $statusUpdated = true;
                    }
                }
            }
        }
        
        if ($statusUpdated) {
            $this->info('Slots updated successfully.');
        } else {
            $this->info('No slots were updated.');
        }
    }

}
