<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Patient;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Record;
use App\Models\Slot;

class RecordController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $provinces = Province::all();
        $patients = Patient::all();
    
        if ($userRole == '2') {
            return view('layouts.Medstaff.record', compact('provinces', 'patients'));
        } elseif ($userRole == '3') {
            return view('layouts.Doctor.record', compact('provinces', 'patients'));
        } else {
            abort(403);
        }
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
        try {
            // Create a new user
            $user = new User;
            $user->name = $request->input('first_name') . ' ' . $request->input('last_name');
            $user->email = $request->input('email');
            $user->role = 1; // Set role to 1 for patient
            $password = Str::random(10); // Generate a random password
            $user->password = Hash::make($password);
            $user->remember_token = Str::random(10); // Generate a remember token
            $user->save();
    
            // Create a new patient
            $patient = new Patient;
            $patient->user_id = $user->id;
            $patient->first_name = $request->input('first_name');
            $patient->middle_name = $request->input('middle_name') ?? null;
            $patient->last_name = $request->input('last_name');
            $patient->contact_number = $request->input('contact_number');
            $patient->gender = $request->input('gender');
            $patient->date_of_birth = $request->input('date_of_birth');
            $patient->status = $request->input('status');
            $patient->province_id = $request->input('province_id');
            $patient->municipality_id = $request->input('municipality_id');
            $patient->barangay_id = $request->input('barangay_id');
            $patient->save();
    
            $this->sendTestEmail($user->email, $password);
    
            return redirect()->route('medical_records.index')->with('success', 'Patient registered successfully.');
        } catch (\Exception $e) {
            
            Log::error('Error registering patient: ' . $e->getMessage());
    
            return redirect()->back()->with('error', 'Failed to register patient. Please try again later.');
        }
    }
    
    

    public function edit($id)
    {
        $provinces = Province::all();
        $municipalities = Municipality::all();
        $barangays = Barangay::all();
        $patient = Patient::findOrFail($id); 
        return view('layouts.Medstaff.recordEdit', compact('patient', 'provinces', 'municipalities', 'barangays'));
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

    public function deletePatient($id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return redirect()->back()->with('error', 'Patient record not found');
        }
        $patient->delete();
        return redirect()->back()->with('success', 'Patient record deleted successfully');
    }

    public function showArchives()
    {
        $softDeletedPatients = Patient::onlyTrashed()->get();
        return view('layouts.Medstaff.restore', ['softDeletedPatients' => $softDeletedPatients]);
    }
    public function restore($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->back()->with('success', 'Patient restored successfully.');
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

                return redirect()->route('appointments.today', $patient->id)->with('success', 'Patient record created successfully');
            } else {
                return redirect()->back()->with('error', 'No appointment found for the patient.');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Patient not found.');
        }
    }

    public function updateRecord(Request $request, $id)
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

            $record->appointment->type = $request->input('type');
            $record->appointment->save();

            foreach ($record->appointment->complaints as $complaint) {
                $complaint->details = $request->input('complaint_details');
                $complaint->save();
            }
            return redirect()->back()->with('success', 'Record updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Record not found.');
        }
    }

    public function destroy($id)
    {
        $record = Record::findOrFail($id);
        $record->delete();
        return redirect()->back()->with('success', 'Record deleted successfully');
    }

    public function sendTestEmail($email, $password)
{
    $data = [
        'personalizations' => [
            [
                'to' => [
                    [
                        'email' => $email,
                    ],
                ],
            ],
        ],
        'from' => [
            'email' => 'your_email@example.com', // Your email here
        ],
        'subject' => 'Password',
        'content' => [
            [
                'type' => 'text/plain',
                'value' => 'Your account has been created. Feel free to change your password once you logged in to Carepoint Clinic Website. Your password is: ' . $password,
            ],
        ],
    ];

    // Replace 'your_sendgrid_api_key_here' with your SendGrid API key
    $response = Http::withHeaders([
        'Authorization' => 'Bearer your_sendgrid_api_key_here',
        'Content-Type' => 'application/json',
    ])->post('https://api.sendgrid.com/v3/mail/send', $data);

    if ($response->successful()) {
        return "Test email sent successfully!";
    } else {
        return "Failed to send test email: " . $response->status();
    }
}

}
