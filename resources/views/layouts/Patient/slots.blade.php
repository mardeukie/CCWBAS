@extends('layouts.Patient.navbar')

@section('content')
    <style>
        #calendar {
            max-width: 800px;
            margin: 0 auto;
        }

        .fc-event {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        .fc-time-grid-event {
            cursor: pointer;
        }
        .suggestions {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .suggestion-item {
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }

    </style>
    <div id="calendar"></div>

    <div id="BookingModal" class="modal" style="display: none;">
        <form id="BookingForm" method="post" action="{{ route('book.appointment') }}">
            @csrf
            <input type="hidden" name="slot_id" id="slot_id">
            <!-- Hidden input field for selected date -->
            <input type="hidden" name="selected_date" id="selectedDate">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Book Appointment</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Appointment Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="consultation">General Consultation</option>
                                <option value="checkup">Checkup</option>
                                <option value="follow-up">Follow-up Visit</option>
                                <option value="vaccination">Tetanus Vaccination</option>
                                <option value="urgent">Urgent Care</option>
                                <option value="medcert">Medical Certification</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Appointment Details</label>
                            <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
                            <ul class="suggestions"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="BookingConfirmationModal" class="modal" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Booking</h5>
            </div>
            <div class="modal-body">
                <form id="GCalendarForm" method="post" action="{{ route('create.event') }}">
                    @csrf
                    <input type="hidden" name="selected_date" id="gcalendar_selectedDate">
                    <p>Do you want to sync this appointment with your Google Calendar?</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="syncGoogleCalendarBtn">Yes</button>
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
    <script src="https://apis.google.com/js/api.js"></script>
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = @json($events);
            
            var today = new Date();
            today.setHours(0, 0, 0, 0); 
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                }, 
                events: events,
                selectable: true,
                select: function(arg) {
                    var start = arg.start;

                    // Ensure selected date is not in the past
                    if (start < today) {
                        alert('You cannot book appointments for past dates.');
                        return;
                    }

                    var selectedSlot = events.find(function(event) {
                        var eventStart = new Date(event.start);
                        return start.getFullYear() === eventStart.getFullYear() &&
                            start.getMonth() === eventStart.getMonth() &&
                            start.getDate() === eventStart.getDate();
                    });

                    if (!selectedSlot) {
                        alert('No slots available for this date.');
                        return;
                    }

                    $('#slot_id').val(selectedSlot.id);
                    
                    // Set the value of the hidden input field with the selected date
                    $('#selectedDate').val(start.toISOString());

                    $('#BookingModal').modal('toggle');
                },

                validRange: {
                    start: today 
                },
                eventContent: function (arg) {
                    var eventEl = document.createElement('div');
                    eventEl.classList.add('fc-event');

                    var statusEl = document.createElement('div');
                    statusEl.classList.add('fc-event-status');
                    statusEl.innerText = arg.event.title; 
                    eventEl.appendChild(statusEl);
                    
                    var slotsEl = document.createElement('div');
                    slotsEl.classList.add('fc-event-slots');
                    slotsEl.innerText = arg.event.extendedProps.slots; 
                    eventEl.appendChild(slotsEl);
                    
                    return { domNodes: [eventEl] };
                }
            });

            calendar.render();

            $('#BookingForm').submit(function(event) {
                event.preventDefault();
                var appointmentType = $('#type').val();
                var appointmentDetails = $('#details').val();
                var selectedDate = $('#selectedDate').val();

                // Client-side validation
                if (!appointmentType || !appointmentDetails) {
                    alert('Please fill in all fields.');
                    return;
                }

                $.ajax({
                    url: '{{ route('book.appointment') }}',
                    method: 'POST',
                    data: $(this).serialize(), 
                    success: function(response) {
                        console.log('Appointment saved successfully:', response);
                        swal("Success", "Appointment booked successfully!", "success").then((value) => {
                            // If user chooses to sync with Google Calendar
                            if (value) {
                                $('#BookingConfirmationModal').modal('toggle');
                            } else {
                                // If user chooses not to sync, hide both modals
                                $('#BookingModal').modal('hide');
                                $('#BookingConfirmationModal').modal('hide');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving appointment:', error);
                        alert('An error occurred while saving the appointment. Please try again later.');
                    }
                });
                    
            });

            $('#syncGoogleCalendarBtn').click(function () {
        // Get the value of the selectedDate input field from the bookingModal
        var selectedDate = $('#selectedDate').val();
        $('#gcalendar_selectedDate').val(selectedDate);
        $('#GCalendarForm').submit();
        window.location.href = '{{ route('google.auth') }}';
    });
        });
        const textarea = document.getElementById('details');
        const suggestionsList = document.querySelector('.suggestions');

        const suggestions = ['cold', 'flu', 'sore throat', 'allergies', 'minor infections', 'fever', 'cough', 'headaches', 'body aches', 'diabetes', 'hypertension', 'asthma', 'routine check-up', 'monthly check-up', 'dosage adjustments', 'discussion of previous test', 'booster shots', 'sprains', 'strains', 'minor burns', 'cuts', 'fractures', 'UTI', 'ear infections', 'skin infections', 'eye infections', 'for sick leave', 'fitness test', 'school', 'work' ];

        textarea.addEventListener('input', function() {
            const inputText = this.value.trim().toLowerCase();
            const filteredSuggestions = suggestions.filter(suggestion =>
                suggestion.toLowerCase().startsWith(inputText)
            );
            displaySuggestions(filteredSuggestions);
        });

        function displaySuggestions(suggestions) {
            const html = suggestions.map(suggestion => `<li class="suggestion-item">${suggestion}</li>`).join('');
            suggestionsList.innerHTML = html;

            const suggestionItems = document.querySelectorAll('.suggestion-item');
            suggestionItems.forEach(item => {
                item.addEventListener('click', function() {
                    textarea.value = this.textContent;
                    suggestionsList.innerHTML = '';
                });
            });
        }

    </script>
@endsection
