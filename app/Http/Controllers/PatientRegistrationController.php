<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class PatientRegistrationController extends Controller
{
    public function showForm()
    {
        $provinces = Province::all();
        return view('layouts.Patient.Registration', compact('provinces'));
    }

    public function register(Request $request)
    {
        $user = Auth::user();

        $patient = new Patient;
        $patient->user_id = $user->id;
        $patient->first_name = $request->input('first_name');
        
        // Check if middle name is empty, if so, assign null
        $middleName = $request->input('middle_name');
        $patient->middle_name = $middleName !== '' ? $middleName : null;

        $patient->last_name = $request->input('last_name');
        $patient->contact_number = $request->input('contact_number');
        $patient->gender = $request->input('gender');
        $patient->date_of_birth = $request->input('date_of_birth');
        $patient->status = $request->input('status');
        $patient->province_id = $request->input('province_id');
        $patient->municipality_id = $request->input('municipality_id');
        $patient->barangay_id = $request->input('barangay_id');
        $patient->save();

        return redirect()->route('patient')->with('success', 'Patient registered successfully.');
    }


    //address
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
}
