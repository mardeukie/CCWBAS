<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Medstaff;

class MedstaffRegistrationController extends Controller
{
    public function showForm()
    {
        return view('layouts.Medstaff.Registration');
    }

    public function register(Request $request)
    {       
        $user = Auth::user(); 

        $medstaff = new Medstaff;
        $medstaff->user_id = $user->id;
        $medstaff->first_name = $request->input('first_name'); 
        $medstaff->middle_name = $request->input('middle_name');
        $medstaff->last_name = $request->input('last_name');
        $medstaff->contact_number = $request->input('contact_number');
        $medstaff->license_number = $request->input('license_number');
        $medstaff->save();
        
        return redirect()->route('medstaff');
    }
}
