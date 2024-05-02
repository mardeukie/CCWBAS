<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Patient;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Appointment;
use App\Models\Record;
use App\Models\Slot;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function index()
    {
        $appointments = Appointment::whereHas('slot.bookingLimit', function ($query) {
            $query->whereDate('date', now()->toDateString());
        })->get();

        $appointmentsTodayCount = count($appointments);

        return view('layouts.Doctor.appointment', [
            'appointments' => $appointments,
            'appointmentsTodayCount' => $appointmentsTodayCount
        ]);
    }

    public function calendar()
    {
        return view('layouts.Doctor.calendar'); 
    }
    // Address
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

    public function register(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
        } else {
            $userId = null; 
        }

        $patient = new Patient;
        $patient->user_id = $userId;
        $patient->first_name = $request->input('first_name');
        $patient->middle_name = $request->input('middle_name');
        $patient->last_name = $request->input('last_name');
        $patient->contact_number = $request->input('contact_number');
        $patient->gender = $request->input('gender');
        $patient->date_of_birth = $request->input('date_of_birth');
        $patient->status = $request->input('status');
        $patient->province_id = $request->input('province_id');
        $patient->municipality_id = $request->input('municipality_id');
        $patient->barangay_id = $request->input('barangay_id');
        $patient->save();

        return redirect()->route('doctor.records')->with('success', 'Patient registered successfully.');
    }

    public function edit($id)
    {
        $provinces = Province::all();
        $municipalities = Municipality::all();
        $barangays = Barangay::all();
        $patient = Patient::findOrFail($id); 
        return view('layouts.Doctor.recordEdit', compact('patient', 'provinces', 'municipalities', 'barangays'));
    }


    public function update(Request $request, $id)
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

        return redirect()->route('patients.edit', $patient->id)->with('success', 'Patient information updated successfully');
    }

    public function store(Request $request, $id)
{
    try {
        $patient = Patient::findOrFail($id);

        $appointment = $patient->appointments()->latest()->first();

        if ($appointment) {
            // Check if the appointment is cancelled or no show
            if ($appointment->status === 'cancelled' || $appointment->status === 'no show') {
                return redirect()->back()->with('error', 'Cannot create a record for a cancelled or no show appointment.');
            }

            $existingRecord = Record::where('appointment_id', $appointment->id)->first();

            if ($existingRecord) {
                return redirect()->back()->with('error', 'A record for this appointment already exists.');
            }

            $date = $appointment->slot->bookingLimit->date;

            $record = Record::create([
                'patient_id' => $patient->id,
                'doctor_id' => 1, 
                'medstaff_id' => 1, 
                'appointment_id' => $appointment->id,
                'vital_signs' => json_encode([
                    'blood_pressure' => $request->input('blood_pressure'),
                    'heart_rate' => $request->input('heart_rate'),
                    'temperature' => $request->input('temperature'),
                    'height' => $request->input('height'),
                    'weight' => $request->input('weight'),
                ]),
                'diagnosis' => $request->input('diagnosis'),
                'treatments' => $request->input('treatments'),
                'medications' => $request->input('medications'),
                'referral' => $request->input('referral'),
                'notes' => $request->input('notes'),
                'date' => $date,
            ]);

            return redirect()->route('patients.edit', $patient->id)->with('success', 'Patient record created successfully');
        } else {
            return redirect()->back()->with('error', 'No appointment found for the patient.');
        }
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'Patient not found.');
    }
}


    public function recordUpdate(Request $request, $id)
    {
        try {
            $record = Record::findOrFail($id);
            $record->update([
                'blood_pressure' => $request->input('blood_pressure'),
                'heart_rate' => $request->input('heart_rate'),
                'temperature' => $request->input('temperature'),
                'height' => $request->input('height'),
                'weight' => $request->input('weight'),
                'diagnosis' => $request->input('diagnosis'),
                'treatments' => $request->input('treatments'),
                'medications' => $request->input('medications'),
                'referral' => $request->input('referral'),
                'notes' => $request->input('notes'),
            ]);

            $record->appointment->type = $request->input('appointment_type');
            $record->appointment->save();

            foreach ($record->appointment->complaints as $complaint){
                $complaint->details = $request->input('complaint_details');
                $complaint->save();
            }

            return redirect()->back()->with('success', 'Record updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        }
    }

    public function doctorDestroy($id)
    {
        $record = Record::findOrFail($id);
        $record->delete();
        return redirect()->back()->with('success', 'Record deleted successfully');
    }

    public function doctorDeletePatient($id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return redirect()->back()->with('error', 'Patient record not found');
        }
        $patient->delete();
        return redirect()->back()->with('success', 'Patient record deleted successfully');
    }
    public function generateReport()
    {
        try {
            $timePeriod = 'monthly';
            $distinctMonths = Appointment::selectRaw('DATE_FORMAT(booking_limits.date, "%Y-%m") as month')
                ->join('slots', 'appointments.slot_id', '=', 'slots.id')
                ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('month');
    
            $appointmentCounts = [];
    
            foreach ($distinctMonths as $month) {
                $startOfMonth = Carbon::parse($month)->startOfMonth();
                $endOfMonth = Carbon::parse($month)->endOfMonth();
    
                $appointments = Appointment::select('appointments.*')
                    ->join('slots', 'appointments.slot_id', '=', 'slots.id')
                    ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                    ->whereBetween('booking_limits.date', [$startOfMonth, $endOfMonth])
                    ->whereNotIn('appointments.status', ['reschedule']) 
                    ->get();

                $appointmentStatusCounts = $this->countAppointmentsByStatus($appointments);
                $appointmentTypeCounts = $this->countAppointmentsByType($appointments);
    
                $appointmentCounts[$month] = [
                    'status' => $appointmentStatusCounts,
                    'types' => $appointmentTypeCounts,
                ];
            }
    
            return view('layouts.Doctor.reports', compact('appointmentCounts', 'timePeriod'));
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while generating the report.');
        }
    }
    

    private function countAppointmentsByStatus($appointments)
    {
        $statusCounts = [
            'booked' => 0,
            'completed' => 0,
            'no show' => 0,
            'cancelled' => 0,
            'unknown' => 0, 
        ];

        foreach ($appointments as $appointment) {
            $status = $appointment->status ?? 'unknown'; 
            $statusCounts[$status]++;
        }

        return $statusCounts;
    }

    private function countAppointmentsByType($appointments)
    {
        $typeCounts = [
            'consultation' => 0,
            'checkup' => 0,
            'follow-up' => 0,
            'vaccination' => 0,
            'urgent' => 0,
            'medcert' => 0,
            'unknown' => 0,
        ];

        foreach ($appointments as $appointment) {
            $type = $appointment->type ?? 'unknown'; 
            $typeCounts[$type]++;
        }

        return $typeCounts;
    }

    public function show()
    {
        return view('layouts.Doctor.settings');
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
    public function showArchivesRecord()
    {
        $softDeletedPatients = Patient::onlyTrashed()->get();
        return view('layouts.Doctor.archive', ['softDeletedPatients' => $softDeletedPatients]);
    }
    public function restoreRecord($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->back()->with('success', 'Patient restored successfully.');
    }

}
