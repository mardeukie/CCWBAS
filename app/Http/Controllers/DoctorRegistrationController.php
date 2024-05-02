<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;

class DoctorRegistrationController extends Controller
{
    public function showForm()
    {
        return view('layouts.Doctor.Registration');
    }

    public function register(Request $request)
    {       
        $user = Auth::user(); 

        $doctor = new Doctor;
        $doctor->user_id = $user->id;
        $doctor->first_name = $request->input('first_name'); 
        $doctor->middle_name = $request->input('middle_name');
        $doctor->last_name = $request->input('last_name');
        $doctor->contact_number = $request->input('contact_number');
        $doctor->license_number = $request->input('license_number');
        $doctor->save();
        
        return redirect()->route('doctor');
    }
}
