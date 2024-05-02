@extends('layouts.Medstaff.navbar')

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
            padding: 10px;
            font-size: 14px;
            white-space: normal;
        }

        .fc-time-grid-event {
            cursor: pointer;
        }
    </style>
    <div id="calendar"></div>

    <!-- Add Modal -->
    <div id="addModal" class="modal" tabindex="-1" role="dialog">
        <form id="addForm" method="post" action="{{ route('patient.book') }}">
            @csrf
            <input type="hidden" name="slot_id" id="slot_id">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Appointment</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Patient selection interface -->
                        <div class="mb-3">
                            <label for="patientSearch" class="form-label">Search and Select Patient</label>
                            <input class="form-control" list="patientOptions" id="patientSearch" placeholder="Type to search...">
                            <datalist id="patientOptions">
                                <!-- Loop through the patients and create options with both name and id -->
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}" data-id="{{ $patient->id }}">
                                @endforeach
                            </datalist>
                            <input type="hidden" id="selected_patient_id" name="selected_patient_id">

                        </div>
                        <input type="hidden" id="patient_id" name="patient_id"> <!-- Hidden input to store the selected patient's ID -->

                        <!-- Add appointment form goes here -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Appointment Type</label>
                            <select class="form-control" id="type" name="type">
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
                            <textarea class="form-control" id="details" name="details" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

    <script>
        $(document).ready(function () {
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
                },

                selectable: true,
                select: function (arg) {
                    var start = arg.start;
                    var end = arg.end;
                    var allDay = arg.allDay;

                    if (start < today) {
                        return;
                    }

                    var selectedSlot = events.find(function (event) {
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

                    $('#addModal').modal('show');
                    $('#selectedDate').val(start.toISOString());
                },
                validRange: {
                    start: today
                }
            });

            calendar.render();
            $('#addForm').submit(function (event) {
                event.preventDefault();

                var selectedSlotId = $('#slot_id').val();

                if (!selectedSlotId) {
                    console.error('Slot ID is missing.');
                    return;
                }
                var formData = $(this).serialize();

                formData += '&slot_id=' + selectedSlotId;

                $.ajax({
                    url: '{{ route('patient.book') }}',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        console.log('Appointment saved successfully:', response);
                        $('#addModal').modal('hide');

                        swal("Success", "Appointment booked successfully!", "success");
                    },
                    error: function (xhr, status, error) {
                        console.error('Error saving appointment:', error);
                        swal("Error", "Failed to book appointment. Please try again later.", "error");
                    }
                });
            });
            $('#addModal').on('click', '[data-dismiss="modal"]', function (e) {
                $('#addModal').modal('hide');
            });
            $('#patientSearch').on('input', function () {
                var selectedName = $(this).val().trim();
                var selectedOption = $('#patientOptions option').filter(function () {
                    return $(this).val() === selectedName;
                });

                if (selectedOption.length > 0) {
                    var selectedId = selectedOption.data('id');
                    $('#selected_patient_id').val(selectedId); 
                } else {
                    $('#selected_patient_id').val(''); 
                }
            });
        });
    </script>
@endsection
