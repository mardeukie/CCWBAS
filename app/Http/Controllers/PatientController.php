<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingLimit;
use App\Models\Slot;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Complaint;
use App\Models\Record;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index()
    {
        $events = array();
        $slots = Slot::with(['bookingLimit'])->get();

        foreach ($slots as $slot) {
            $start_datetime = $slot->bookingLimit->date . ' ' . $slot->start_time;
            $end_datetime = $slot->bookingLimit->date . ' ' . $slot->end_time;
            $title = $slot->bookingLimit->slot_number . ' - ' . $slot->status; 

            $events[] = [
                'id' => $slot->id, 
                'title' => $slot->status,
                'slots' => $slot->bookingLimit->slot_number,
                'start' => $start_datetime,
                'end' => $end_datetime,
            ];
        }

        return view('layouts.Patient.slots', ['events' => $events]);
    }

    public function calendar()
    {
        return view('layouts.Patient.calendar'); 
    }

    public function bookAppointment(Request $request)
    {
        try {
            $request->validate([
                'slot_id' => 'required|numeric',
                'type' => 'required|string',
                'details' => 'required|string',
            ]);

            $patient = Patient::where('user_id', auth()->id())->first();

            if (!$patient) {
                return response()->json(['error' => 'Authenticated patient not found.'], 404);
            }

            $patientId = $patient->id;

            $existingAppointment = Appointment::where('patient_id', $patientId)
                ->where('slot_id', $request->input('slot_id'))
                ->first();

            if ($existingAppointment) {
                return response()->json(['error' => 'Patient already has an appointment for this slot.'], 403);
            }

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
        
            return redirect()->route('patient.slots')->with('success', 'Appointment booked successfully');
        } catch (\Exception $e) {
            \Log::error('Error booking appointment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to book appointment. Please try again.', 'exception' => $e->getMessage()], 500);
        }
    }

    public function getAppointments()
    {
        $patient = Auth::user()->patient;
        $appointments = $patient->appointments()->orderBy('created_at', 'asc')->get();
        return view('layouts.Patient.appointment', compact('appointments', 'patient'));
    }

    public function show()
    {
        $patient = Auth::user()->patient; 
        $records = $patient->records; 

        $provinces = Province::all();
        $municipalities = Municipality::all();
        $barangays = Barangay::all();

        return view('layouts.Patient.record', compact('patient', 'records', 'provinces', 'municipalities', 'barangays'));
    }

    //Address
    public function getProvinces()
    {
        $provinces = Province::all();
        return $provinces;
    }

    public function getMunicipalities(Request $request)
    {
        $municipalities = Municipality::where('province_id', $request->province_id)->get();

        if (count($municipalities) > 0) {
            return response()->json($municipalities);
        }
    }

    public function getBarangays(Request $request)
    {
        $barangays = Barangay::where('municipality_id', $request->municipality_id)->get();

        if (count($barangays) > 0) {
            return response()->json($barangays);
        }
    }

    public function updateInfo(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'contact_number' => $request->input('contact_number'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'status' => $request->input('status'),
            'province_id' => $request->input('province_id'),
            'municipality_id' => $request->input('municipality_id'),
            'barangay_id' => $request->input('barangay_id'), 
        ]);

        return redirect()->route('patient.show', $patient->id)->with('success', 'Patient information updated successfully');
    }
    public function cancelAppointment($appointmentId) {
        $appointment = Appointment::find($appointmentId);
    
        if ($appointment) {
            $appointment->status = 'cancelled';
            $appointment->save();
    
            $bookingLimit = $appointment->slot->bookingLimit;
    
            if ($bookingLimit) {
                $bookingLimit->slot_number += 1;
                $bookingLimit->save();
            }

            $appointments = Appointment::all();
    
            return view('layouts.Patient.appointment', compact('appointments'))->with('message', 'Appointment cancelled successfully.');
        } else {
            return view('layouts.Patient.appointment')->with('message', 'Appointment not found.')->with('status', 404);
        }
    }

    public function showSettings()
    {
        return view('layouts.Patient.settings');
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

    public function rescheduleAppointment($appointmentId) {
        $appointment = Appointment::find($appointmentId);
    
        if ($appointment) {
            $appointment->status = 'reschedule';
            $appointment->save();
    
            $bookingLimit = $appointment->slot->bookingLimit;
    
            if ($bookingLimit) {
                $bookingLimit->slot_number += 1;
                $bookingLimit->save();
            }
    
            $appointments = Appointment::all();

            return redirect()->route('patient.slots')->with('reschedule_success', 'Appointment rescheduled successfully');
        } else {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }
    }
    
    

}



