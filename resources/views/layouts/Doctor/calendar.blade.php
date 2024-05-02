@extends('layouts.Doctor.navbar')

@section('content')
<style>
    #calendar-container {
        max-width: 800px;
        margin: 0 auto;
    }

    #calendar {
        margin-bottom: 20px;
    }

    .fc-event {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff;
    }

    .fc-time-grid-event {
        cursor: pointer;
    }

    #authorize_button,
    #signout_button {
        display: block;
        margin-top: 20px;
        margin-bottom: 10px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #authorize_button:hover,
    #signout_button:hover {
        background-color: #0056b3;
    }

</style>
<div id="calendar-container">
    <button id="authorize_button" onclick="handleAuthClick()">Sync Google Calendar</button>
    <button id="signout_button" onclick="handleSignoutClick()">Sign Out</button>
    <div id="calendar"></div>
</div>

<!-- Add a modal for creating events -->
<div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createEventModalLabel">Create Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form fields for event details -->
      <form id="eventForm">
          <div class="form-group">
              <label for="eventTitle">Event Title</label>
              <input type="text" class="form-control" id="eventTitle" required>
          </div>
          <div class="form-group">
              <label for="eventDate">Date</label>
              <input type="date" class="form-control" id="eventDate" required>
          </div>
          <div class="form-group">
              <label for="eventDescription">Description</label>
              <textarea class="form-control" id="eventDescription" rows="3"></textarea>
          </div>
          <div class="form-group">
              <label for="eventLocation">Location</label>
              <input type="text" class="form-control" id="eventLocation">
          </div>
      </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveEventButton">Save</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.10.1/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.10.1/main.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.10.1/main.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.10.1/main.min.css" rel="stylesheet" />
<script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
<script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>

<script type="text/javascript">
      /* exported gapiLoaded */
      /* exported gisLoaded */
      /* exported handleAuthClick */
      /* exported handleSignoutClick */

      // TODO: Set to client ID and API key from the Developer Console
      const CLIENT_ID = 'your_google_client_id';
      const API_KEY = 'your_google_api_key';

      // Discovery doc URL for APIs used by the quickstart
      const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest';

      // Authorization scopes required by the API; multiple scopes can be
      // included, separated by spaces.
      const SCOPES = 'https://www.googleapis.com/auth/calendar';

      let tokenClient;
      let gapiInited = false;
      let gisInited = false;

      document.getElementById('authorize_button').style.visibility = 'hidden';
      document.getElementById('signout_button').style.visibility = 'hidden';

      /**
       * Callback after api.js is loaded.
       */
      function gapiLoaded() {
        gapi.load('client', initializeGapiClient);
      }

      /**
       * Callback after the API client is loaded. Loads the
       * discovery doc to initialize the API.
       */
      async function initializeGapiClient() {
        await gapi.client.init({
          apiKey: API_KEY,
          discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
        maybeEnableButtons();
      }

      /**
       * Callback after Google Identity Services are loaded.
       */
      function gisLoaded() {
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: CLIENT_ID,
          scope: SCOPES,
          callback: '', // defined later
        });
        gisInited = true;
        maybeEnableButtons();
      }

      /**
       * Enables user interaction after all libraries are loaded.
       */
      function maybeEnableButtons() {
        if (gapiInited && gisInited) {
          document.getElementById('authorize_button').style.visibility = 'visible';
        }
      }

      /**
       *  Sign in the user upon button click.
       */
      function handleAuthClick() {
        tokenClient.callback = async (resp) => {
          if (resp.error !== undefined) {
            throw (resp);
          }
          document.getElementById('signout_button').style.visibility = 'visible';
          document.getElementById('authorize_button').innerText = 'Refresh';
          await listUpcomingEvents();
        };

        if (gapi.client.getToken() === null) {
          // Prompt the user to select a Google Account and ask for consent to share their data
          // when establishing a new session.
          tokenClient.requestAccessToken({prompt: 'consent'});
        } else {
          // Skip display of account chooser and consent dialog for an existing session.
          tokenClient.requestAccessToken({prompt: ''});
        }
      }

      /**
       *  Sign out the user upon button click.
       */
      function handleSignoutClick() {
        const token = gapi.client.getToken();
        if (token !== null) {
          google.accounts.oauth2.revoke(token.access_token);
          gapi.client.setToken('');
          document.getElementById('content').innerText = '';
          document.getElementById('authorize_button').innerText = 'Authorize';
          document.getElementById('signout_button').style.visibility = 'hidden';
        }
      }
      /**
       * Print the summary and start datetime/date of the next ten events in
       * the authorized user's calendar. If no events are found an
       * appropriate message is printed.
       */
      async function listUpcomingEvents() {
        let response;
        try {
            const request = {
                'calendarId': 'primary',
                'timeMin': (new Date()).toISOString(),
                'showDeleted': false,
                'singleEvents': true,
                'maxResults': 10,
                'orderBy': 'startTime',
            };
            response = await gapi.client.calendar.events.list(request);
        } catch (err) {
            console.error('Error fetching events:', err);
            return;
        }

        const events = response.result.items;
        if (!events || events.length === 0) {
            console.log('No events found.');
            return;
        }

        // Format events for FullCalendar
        const formattedEvents = events.map(event => ({
            id: event.id,
            title: event.summary,
            start: event.start.dateTime || event.start.date,
            end: event.end.dateTime || event.end.date,
            backgroundColor: '#3498db', // Customize as needed
            borderColor: '#3498db', // Customize as needed
            textColor: '#ffffff', // Customize as needed
        }));

        const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        events: formattedEvents,
        dateClick: function(info) { 
            openEventModal(info.dateStr); // Pass the clicked date to openEventModal
        }
    });
    calendar.render();
        function openEventModal(date) {
        $('#eventForm')[0].reset(); 
        $('#eventDate').val(date); 
        $('#createEventModal').modal('show'); 
    }


    // Function to handle event creation
    window.createNewEvent = function() {
    const event = {
        'summary': $('#eventTitle').val(),
        'location': $('#eventLocation').val(),
        'description': $('#eventDescription').val(),
        'start': {
            'date': $('#eventDate').val(), 
            'timeZone': 'Asia/Manila' 
        },
        'end': {
            'date': $('#eventDate').val(), 
            'timeZone': 'Asia/Manila' 
        },
        'reminders': {
            'useDefault': true
        }
    };

    const request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event
    });

    request.execute(function(event) {
        console.log('Event created: ' + event.htmlLink);
        $('#createEventModal').modal('hide'); 
    });
}

    // Event listener for form submission
    $('#saveEventButton').on('click', function(event) {
        event.preventDefault(); // Prevent default form submission
        window.createNewEvent(); // Call function to create event
    });
    }
    </script>
@endsection
