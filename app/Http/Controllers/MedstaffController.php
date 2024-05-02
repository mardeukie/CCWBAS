<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Medstaff;
use App\Models\BookingLimit;
use App\Models\Slot;
use App\Models\Appointment;
use App\Models\Complaint;
use App\Models\Patient;
use App\Models\SmsTracking;
use Twilio\Rest\Client;
use Carbon\Carbon;

class MedstaffController extends Controller
{
    public function getSlots()
    {
        $slots = Slot::with('bookingLimit')->get();
        return view('layouts.Medstaff.slots', compact('slots'));
    }

    public function calendar()
    {
        $events = array();
        $slots = Slot::with(['bookingLimit'])->get();
        
        foreach ($slots as $slot) {
            $start_datetime = $slot->bookingLimit->date . ' ' . $slot->start_time;
            $end_datetime = $slot->bookingLimit->date . ' ' . $slot->end_time;

            $events[] = [
                'id' => $slot->id, 
                'title' => $slot->status,
                'slots' => $slot->bookingLimit->slot_number,
                'start' => $start_datetime,
                'end' => $end_datetime,
            ];
        }
        
        $patients = Patient::all(); 
        
        return view('layouts.Medstaff.calendar', ['events' => $events, 'patients' => $patients]);
    }

    public function bookAppointment(Request $request)
    {
        try {
            $request->validate([
                'slot_id' => 'required|numeric',
                'type' => 'required|string',
                'details' => 'required|string',
                'selected_patient_id' => 'required|numeric', 
            ]);
            
            $medstaff = Medstaff::where('user_id', auth()->id())->first();

            if (!$medstaff) {
                return response()->json(['error' => 'Authenticated Medstaff not found.'], 404);
            }

            $patientId = $request->input('selected_patient_id');

            $slot = Slot::find($request->input('slot_id'));

            if (!$slot) {
                return response()->json(['error' => 'Slot not found.'], 404);
            }

            $bookingLimit = $slot->bookingLimit;

            if (!$bookingLimit || $bookingLimit->slot_number <= 0) {
                return response()->json(['error' => 'No available slots for this date.'], 403);
            }

            $appointment = new Appointment([
                'patient_id' => $patientId, 
                'slot_id' => $request->input('slot_id'),
                'type' => $request->input('type'),
                'status' => 'booked',
            ]);
            $appointment->save();

            $complaint = new Complaint([
                'patient_id' => $patientId,
                'appointment_id' => $appointment->id,
                'details' => $request->input('details'),
            ]);
            $complaint->save();

            $bookingLimit->decrement('slot_number');

        } catch (\Exception $e) {
            \Log::error('Error booking appointment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to book appointment. Please try again.', 'exception' => $e->getMessage()], 500);
        }
    }

    public function createBooking(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'slot_number' => 'required|numeric',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'from' => 'required|date_format:Y-m-d',
        'to' => 'required|date_format:Y-m-d|after_or_equal:from', 
    ]);

    try {
        DB::beginTransaction();

        $user = Auth::user();
        $medstaff = Medstaff::where('user_id', $user->id)->first(); 

        if (!$medstaff) {
            return redirect()->route('medstaff.slots')->with('error', 'You are not authorized to generate slots');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('from'));
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('to'));

        while ($startDate->lte($endDate)) {
            $bookingLimit = BookingLimit::updateOrCreate([
                'date' => $startDate->format('Y-m-d')
            ], [
                'slot_number' => $request->input('slot_number')
            ]);

            $slot = Slot::create([
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'booking_limit_id' => $bookingLimit->id,
                'medstaff_id' => $medstaff->id,
            ]);

            $startDate->addDay();
        }

        DB::commit();

        return redirect()->route('medstaff.slots')->with('success', 'Booking slots generated successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        // Log the error
        logger()->error($e->getMessage());
        return redirect()->route('medstaff.slots')->with('error', 'An error occurred while generating slots');
    }
}

    
    public function destroySlot($id)
    {
        Slot::destroy($id);
        return redirect()->route('medstaff.slots')->with('success', 'Slot deleted successfully');
    }
    public function editSlot($id)
    {
        $slot = Slot::with('bookingLimit')->findOrFail($id);
        return view('layouts.Medstaff.slots', compact('slot'));
    }

    public function updateSlot(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'slot_number' => 'required|numeric',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        $slot = Slot::findOrFail($id);
        $slot->booking_limit_id = BookingLimit::updateOrCreate(
            ['date' => $request->input('date')],
            ['slot_number' => $request->input('slot_number')]
        )->id;

        $slot->start_time = $request->input('start_time');
        $slot->end_time = $request->input('end_time');
        $slot->save();
        return redirect()->route('medstaff.slots')->with('success', 'Slot updated successfully');
    }
    public function index()
    {
        $appointments = Appointment::all();
        return view('layouts.Medstaff.appointment', ['appointments' => $appointments]);
    }
    
    public function viewAppointmentsForToday()
    {
        $appointments = Appointment::whereHas('slot.bookingLimit', function ($query) {
            $query->whereDate('date', now()->toDateString());
        })->where('status', 'booked')->get();

        $appointmentsTodayCount = count($appointments);

        return view('layouts.Medstaff.appointmentToday', [
            'appointments' => $appointments,
            'appointmentsTodayCount' => $appointmentsTodayCount
        ]);
    }

    public function viewAppointmentsForTomorrow()
    {
        $appointments = Appointment::whereHas('slot.bookingLimit', function ($query) {
            $query->whereDate('date', now()->addDay()->toDateString()); 
        })->where('status', 'booked')->get();

        $appointmentsTomorrowCount = count($appointments);

        return view('layouts.Medstaff.upcomingAppointment', [
            'appointments' => $appointments,
            'appointmentsTomorrowCount' => $appointmentsTomorrowCount
        ]);
    }



    public function updateStatus(Request $request, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:completed,no show,cancelled',
        ]);
        
        $appointment = Appointment::find($appointmentId);
        
        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found.');
        }
        
        $previousStatus = $appointment->status;
    
        $appointment->status = $request->status;
        $appointment->save();
        
        if ($previousStatus !== 'cancelled' && $request->status === 'cancelled') {
            $receiverPhone = $appointment->patient->contact_number;
            $this->sendCancellationSms($receiverPhone, $appointmentId);
        }
    
    
        if ($previousStatus !== 'cancelled' && $request->status === 'cancelled') {
            $bookingLimit = $appointment->slot->bookingLimit;
            if ($bookingLimit) {
                $bookingLimit->slot_number += 1;
                $bookingLimit->save();
            }
        }
    
        return redirect()->route('booked.appointments')->with('success', 'Appointment status updated successfully.');
    }

    public function sendCancellationSms($receiverPhone, $appointmentId)
    {
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $sendernumber = getenv("TWILIO_PHONE");
    
        $twilio = new Client($sid, $token);
    
        try {
            $appointment = Appointment::findOrFail($appointmentId);
            $patientName = $appointment->patient->first_name;
            $appointmentDate = Carbon::parse($appointment->slot->bookingLimit->date);
            $messageBody = "Hi $patientName, your appointment scheduled for {$appointmentDate->format('l, F jS, Y')} at Carepoint Medical Clinic has been canceled. We apologize for any inconvenience caused. Please contact us for further assistance.";
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
    
    public function show()
    {
        return view('layouts.Medstaff.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully');
    }

    public function updateSlotStatus()
    {
        Artisan::call('slots:update');   
        return redirect()->route('medstaff.slots')->with('success', 'Slots status updated successfully');
    }

    
}
