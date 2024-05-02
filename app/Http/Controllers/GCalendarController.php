<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;

class GCalendarController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        // Set the client secret from a safe location
        $client->setAuthConfig('your_client_secret_path_here');
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

        $client->setRedirectUri('http://localhost:8000/google/redirect');

        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        // Set the client secret from a safe location
        $client->setAuthConfig('your_client_secret_path_here');
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

        $client->setRedirectUri('http://localhost:8000/google/redirect');

        $accessToken = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        session(['google_access_token' => $accessToken]);

        $selectedDate = $request->selected_date; // Get selectedDate from the request
        $accessToken = session('google_access_token');

        $this->createEvent($selectedDate, $accessToken);

        return redirect()->route('patient.slots');
    }

    public function createEvent($selectedDate, $accessToken)
    {
        $client = new Google_Client();
        $client->setAccessToken($accessToken);
        $service = new Google_Service_Calendar($client);

        $selectedDate = Carbon::parse($selectedDate, 'UTC');
        $startDate = $selectedDate->copy()->format('Y-m-d\TH:i:s\Z');
        $endDate = $selectedDate->copy()->addHours(1)->format('Y-m-d\TH:i:s\Z');

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Carepoint Clinic Appointment',
            'description' => 'Appointment with Dra. Vanessa',
            'location' => 'Purok Manga, Poblacion 1, Mabini, Bohol',
            'start' => ['dateTime' => $startDate, 'timeZone' => 'UTC'],
            'end' => ['dateTime' => $endDate, 'timeZone' => 'UTC'],
        ]);

        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event);
        return $event;
    }
}
