<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\SmsTracking;
use Carbon\Carbon;

class SmsController extends Controller
{
    public function sendsms(Request $request)
    {
        $appointmentIds = $request->input('appointments', []);

        foreach ($appointmentIds as $appointmentId) {
            $appointment = Appointment::findOrFail($appointmentId);
            $patient = $appointment->patient;

            $receiverPhone = $patient->contact_number;
            $appointmentDate = Carbon::parse($appointment->slot->bookingLimit->date);

            $messageBody = "Hi {$patient->first_name},\n\n"
                         . "This is a reminder from Carepoint Medical Clinic about your appointment scheduled for {$appointmentDate->format('l, F jS, Y')}.\n\n"
                         . "Please be advised to visit us at our clinic located in Purok Mangga, Poblacion 1, Mabini, Bohol.\n\n"
                         . "Our clinic hours are from 6:00-9:00pm on weekdays and 8:00am-6:00pm on weekends.";

            $sid = getenv("TWILIO_SID");
            $token = getenv("TWILIO_TOKEN");
            $sendernumber = getenv("TWILIO_PHONE");

            $twilio = new Client($sid, $token);

            try {
                $message = $twilio->messages->create($receiverPhone,
                    [
                        "body" => $messageBody,
                        "from" => $sendernumber
                    ]
                );

                $smsTracking = new SmsTracking([
                    'direction' => 'outbound',
                    'status' => 'delivered',
                    'date_sent' => now(),
                    'message_sid' => $message->sid, 
                ]);
    
                $appointment->smsTrackings()->save($smsTracking);

                \Log::info("SMS sent to $receiverPhone");
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS to $receiverPhone: " . $e->getMessage());
            }
        }
        return redirect()->route('appointments.tomorrow');
    }
}
