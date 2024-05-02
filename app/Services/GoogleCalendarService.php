<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use App\Models\Appointment;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        // Initialize Google Client
        $this->client = new \Google_Client();
        $this->client->setAuthConfig(config_path('client_secret.json'));
        $this->client->addScope(\Google_Service_Calendar::CALENDAR_EVENTS);
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        try {
            // Exchange authorization code for access token
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            // Set access token to the client
            $this->client->setAccessToken($accessToken);
            
            return $accessToken;
        } catch (\Exception $e) {
            // Log or handle the exception
            \Log::error('Error authenticating with Google Calendar: ' . $e->getMessage());
            return null;
        }
    }

    public function createEvent(Appointment $appointment)
    {
        try {
            // Check if appointment and its related models exist
            if (!$appointment || !$appointment->slot || !$appointment->complaints) {
                throw new \Exception('Invalid appointment data');
            }
    
            // Initialize Google Calendar service
            $service = new \Google_Service_Calendar($this->client);
    
            // Create event data
            $eventData = [
                'summary' => $appointment->type,
                'description' => $appointment->complaints->details,
                // Set start and end time as needed
                'start' => [
                    'dateTime' => $appointment->slot->start_time->toDateTimeString(),
                    'timeZone' => 'Asia/Manila', // Replace with your time zone
                ],
                'end' => [
                    'dateTime' => $appointment->slot->end_time->toDateTimeString(),
                    'timeZone' => 'Asia/Manila', // Replace with your time zone
                ],
            ];
    
            // Create new Google Calendar event
            $event = new \Google_Service_Calendar_Event($eventData);
    
            // Insert event to the primary calendar
            $calendarId = 'primary'; // Use 'primary' for the primary calendar
            $event = $service->events->insert($calendarId, $event);
    
            return $event; // Return the created event
        } catch (\Exception $e) {
            // Log or handle the exception
            \Log::error('Error creating Google Calendar event: ' . $e->getMessage());
            return null;
        }
    }
    

}
