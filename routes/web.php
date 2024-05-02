<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientRegistrationController;
use App\Http\Controllers\MedstaffRegistrationController;
use App\Http\Controllers\DoctorRegistrationController;
use App\Http\Controllers\MedstaffController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\GCalendarController;
use App\Http\Controllers\SmsController;
use App\Mail\Email;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('patient',function(){
    return view('patient');
})->name('patient')->middleware('patient');
Route::get('medstaff',function(){
    return view('medstaff');
})->name('medstaff')->middleware('medstaff');
Route::get('doctor',function(){
    return view('doctor');
})->name('doctor')->middleware('doctor');

//Patient Registration
Route::get('/patient.registration', [PatientRegistrationController::class, 'showForm'])->name('patient.registration');
Route::post('/patient', [PatientRegistrationController::class, 'register'])->name('patient');

//Medstaff Registration
Route::get('/medstaff.registration', [MedstaffRegistrationController::class, 'showForm'])->name('medstaff.registration');
Route::post('/medstaff', [MedstaffRegistrationController::class, 'register'])->name('medstaff');

//Doctor Registration
Route::get('/doctor.registration', [DoctorRegistrationController::class, 'showForm'])->name('doctor.registration');
Route::post('/doctor', [DoctorRegistrationController::class, 'register'])->name('doctor');

//address
Route::get('provinces', [PatientRegistrationController::class, 'getProvinces'])->name('provinces');
Route::get('municipalities', [PatientRegistrationController::class, 'getMunicipalities'])->name('municipalities');
Route::get('barangays', [PatientRegistrationController::class, 'getBarangays'])->name('barangays');

Route::get('/patient/calendar', function () {
    return view('layouts.Patient.calendar');
})->name('patient.calendar');

//calendar
Route::get('/google/auth', [GCalendarController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google/redirect', [GCalendarController::class, 'handleGoogleCallback'])->name('google.redirect');
Route::post('/google-calendar/create-event', [GCalendarController::class, 'createGoogleEvent'])->name('create.event');


//send sms
Route::post("/sendsms",[SmsController::class,'sendsms'])->name('send.reminders');
Route::post("/sendsms/cancel",[SmsController::class,'sendCancellationSms'])->name('cancellation.reminders');

//send email:
Route::get('/send-test-email', 'RecordController@sendTestEmail');
Route::get("/send-test-email",[RecordController::class,'sendTestEmail']);



//Medstaff
Route::middleware(['auth','medstaff'])->group(function () {
    Route::get('/medstaff/slots', [MedstaffController::class, 'getSlots'])->name('medstaff.slots');
    Route::get('/medstaff/calendar', [MedstaffController::class, 'calendar'])->name('medstaff.calendar');
    Route::post('/patient/book', [MedstaffController::class, 'bookAppointment'])->name('patient.book');
    Route::post('/create-booking', [MedstaffController::class, 'createBooking'])->name('create.booking');
    Route::delete('/medstaff/slots/{id}/destroy', [MedstaffController::class, 'destroySlot'])->name('medstaff.destroy.slot');
    Route::get('/medstaff/slots/{id}/edit', [MedstaffController::class, 'editSlot'])->name('medstaff.edit.slot');
    Route::patch('/medstaff/slots/{id}/update', [MedstaffController::class, 'updateSlot'])->name('medstaff.update.slot');
    Route::get('/booked/appointments', [MedstaffController::class, 'index'])->name('booked.appointments');
    Route::get('/appointments/today', [MedstaffController::class, 'viewAppointmentsForToday'])->name('appointments.today');
    Route::get('/appointments/tomorrow', [MedstaffController::class, 'viewAppointmentsForTomorrow'])->name('appointments.tomorrow');
    Route::post('/appointments/{appointment}/status-update', [MedstaffController::class, 'updateStatus'])->name('appointments.update-status');
    Route::get('/medical-records', [RecordController::class, 'index'])->name('medical_records.index');
    Route::get('get-provinces', [RecordController::class, 'getProvinces'])->name('get.provinces');
    Route::get('get-municipalities', [RecordController::class, 'getMunicipalities'])->name('get.municipalities');
    Route::get('get-barangays', [RecordController::class, 'getBarangays'])->name('get.barangays');
    Route::post('/patient/registration', [RecordController::class, 'register'])->name('patient.registration.submit');
    Route::get('/patients/{id}/edit', [RecordController::class, 'edit'])->name('patients.edit');
    Route::patch('/patients/edit/{id}', [RecordController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [RecordController::class, 'deletePatient'])->name('patients.delete');
    Route::post('patients/{id}/record/store', [RecordController::class, 'store'])->name('record.store');
    Route::patch('/records/{id}', [RecordController::class, 'updateRecord'])->name('records.update');
    Route::delete('/records/{id}', [RecordController::class, 'destroy'])->name('records.destroy');
    Route::get('/medstaff', [StatsController::class, 'showDashboard']);
    Route::get('/generate-report', [StatsController::class, 'generateReport'])->name('generate.report');
    Route::get('/restore',[RecordController::class,'showArchives'])->name('restore');
    Route::put('/patients/{id}/restore', [RecordController::class,'restore'])->name('patients.restore');
    Route::get('/settings', [MedstaffController::class,'show'])->name('settings');
    Route::post('/settings', [MedstaffController::class,'updatePassword'])->name('settings.updatePassword');
    Route::get('/getChartData', [StatsController::class, 'getChartData'])->name('getChartData');
    Route::post('/update-slot-status', [MedstaffController::class, 'updateSlotStatus'])->name('update.slot.status');


});

//Patient
Route::middleware(['auth','patient'])->group(function () {
    Route::get('/patient/slots', [PatientController::class, 'index'])->name('patient.slots');
    Route::post('/book-appointment', [PatientController::class, 'bookAppointment'])->name('book.appointment');
    Route::get('/patient/appointment', [PatientController::class, 'getAppointments'])->name('patient.appointments');
    Route::get('/patient/show', [PatientController::class, 'show'])->name('patient.show');
    Route::get('get/provinces', [PatientController::class, 'getProvinces'])->name('provinces.get');
    Route::get('get/municipalities', [PatientController::class, 'getMunicipalities'])->name('municipalities.get');
    Route::get('get/barangays', [PatientController::class, 'getBarangays'])->name('barangays.get');
    Route::patch('/patients/{id}', [PatientController::class, 'updateInfo'])->name('patients.updateInfo');
    Route::put('/appointments/{id}/cancel', [PatientController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/patient/settings', [PatientController::class,'showSettings'])->name('settings.patient');
    Route::post('/patient/settings', [PatientController::class,'updatePassword'])->name('patient.settings');
    Route::put('/appointments/{id}/reschedule', [PatientController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
});

//Doctor
Route::middleware(['auth','doctor'])->group(function () {
    Route::get('/appointments', [DoctorController::class, 'index'])->name('appointment.index');
    Route::get('/doctor/calendar', [DoctorController::class, 'calendar'])->name('doctor.calendar');
    Route::get('/doctor/records', [RecordController::class, 'index'])->name('doctor.records');
    Route::get('patient-provinces', [DoctorController::class, 'getProvinces'])->name('patient.provinces');
    Route::get('patient-municipalities', [DoctorController::class, 'getMunicipalities'])->name('patient.municipalities');
    Route::get('patient-barangays', [DoctorController::class, 'getBarangays'])->name('patient.barangays');
    Route::post('/add/patient', [DoctorController::class, 'register'])->name('newpatient.registration.submit');
    Route::get('/patients/doctor/{id}/edit', [DoctorController::class, 'edit'])->name('doctor.patientEdit');
    Route::patch('/patients/doctor/edit/{id}', [DoctorController::class, 'update'])->name('doctor.patientUpdate');
    Route::post('patients/doctor/{id}/record/store', [DoctorController::class, 'store'])->name('store.patientRecord');
    Route::patch('/records/{id}/doctor', [DoctorController::class, 'recordUpdate'])->name('patient.recordUpdate');
    Route::delete('/patients/{id}/doctor', [DoctorController::class, 'doctorDeletePatient'])->name('records.deletePatient');
    Route::delete('/records/{id}/doctor', [DoctorController::class, 'doctorDestroy'])->name('destroy.records');
    Route::get('/doctor/reports', [DoctorController::class, 'generateReport'])->name('doctor.generateReport');
    Route::get('/doctor/settings', [DoctorController::class,'show'])->name('settings.doctor');
    Route::post('/doctor/settings', [DoctorController::class,'updatePassword'])->name('doctor.settings');
    Route::get('/restore/patient',[DoctorController::class,'showArchivesRecord'])->name('restore.patient');
    Route::put('/restore/{id}/patient', [DoctorController::class,'restoreRecord'])->name('patientsRecord.restore');
    
});
 
//update appointment status
Route::post('/appointment/completed/{id}', [AppointmentController::class, 'updateCompleted'])->name('appointment.completed');
Route::post('/appointment/no-show/{id}', [AppointmentController::class, 'updateNoShow'])->name('appointment.no-show');

