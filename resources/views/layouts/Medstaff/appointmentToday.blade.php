@extends('layouts.Medstaff.navbar')

@section('content')
<div class="container">
    <div class="table-responsive">
        <table id="myDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th colspan="7">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Appointments Today</h1>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th class="text-center">Slot Number</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Patient Name</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Complaint Details</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    @php
                        $slot = $appointment->slot;
                        $previousAppointments = $slot->appointments->where('created_at', '<', $appointment->created_at);
                        $previousAppointmentsCount = $previousAppointments->count();
                        $orderNumber = $previousAppointmentsCount + 1; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $orderNumber }}</td>
                        <td class="text-center">{{ date('M d, Y', strtotime($appointment->slot->bookingLimit->date)) }}</td>
                        <td class="text-center">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</td>
                        <td class="text-center">{{ $appointment->type }}</td>
                        <td class="text-center">{{ $appointment->complaints->isEmpty() ? 'No details available' : $appointment->complaints->first()->details }}</td>
                        <td class="text-center">{{ $appointment->status }}</td>
                        <td class="text-center">
                            @if($appointment->status == 'booked')
                                <form id="updateForm_{{ $appointment->id }}" method="POST" action="{{ route('appointments.update-status', $appointment) }}">
                                    @csrf
                                    <div class="btn-group" role="group" aria-label="Appointment actions">
                                        <button type="button" class="btn btn-success btn-sm" onclick="confirmStatusUpdate('{{ $appointment->id }}', 'completed')"><i class="fas fa-check"></i></button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="confirmStatusUpdate('{{ $appointment->id }}', 'no show')"><i class="fas fa-times"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmStatusUpdate('{{ $appointment->id }}', 'cancelled')"><i class="fas fa-times-circle"></i></button>
                                        <button type="button" class="btn btn-primary btn-sm addRecordBtn" data-appointment-id="{{ $appointment->id }}" data-appointment-date="{{ $appointment->slot->bookingLimit->date }}"><i class="fas fa-plus"></i></button>
                                    </div>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Record Modal -->
@foreach($appointments as $appointment)
<div class="modal fade" id="addRecordModal_{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="addRecordModalLabel_{{ $appointment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecordModalLabel">Add New Medical Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form method="POST" action="{{ route('record.store', $appointment->patient->id) }}">
                @csrf
                <input type="hidden" name="appointment_id" id="appointment_id">
                <!-- Date of Record Input -->
                <div class="form-group">
                    <label for="record_date" class="font-weight-bold">Date</label>
                    <input id="record_date" type="date" class="form-control" name="record_date" required>
                </div>

                <div class="row">
                    <!-- First Column -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_pressure" class="font-weight-bold">Blood Pressure</label>
                                <input id="blood_pressure" type="text" class="form-control" name="blood_pressure" value="{{ old('blood_pressure') }}" required autocomplete="blood_pressure">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="heart_rate" class="font-weight-bold">Heart Rate</label>
                                <input id="heart_rate" type="text" class="form-control" name="heart_rate" value="{{ old('heart_rate') }}" required autocomplete="heart_rate">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="temperature" class="font-weight-bold">Temperature</label>
                                <input id="temperature" type="text" class="form-control" name="temperature" value="{{ old('temperature') }}" required autocomplete="temperature">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="height" class="font-weight-bold">Height</label>
                                <input id="height" type="text" class="form-control" name="height" value="{{ old('height') }}" required autocomplete="height">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="weight" class="font-weight-bold">Weight</label>
                                <input id="weight" type="text" class="form-control" name="weight" value="{{ old('weight') }}" required autocomplete="weight">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="referral" class="font-weight-bold">Referral</label>
                                <textarea id="referral" class="form-control" name="referral" rows="3" required>{{ old('referral') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="font-weight-bold">Notes</label>
                                <textarea id="notes" class="form-control" name="notes" rows="3" required>{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Second Column -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="diagnosis" class="font-weight-bold">Diagnosis</label>
                                <textarea id="diagnosis" class="form-control" name="diagnosis" rows="3" required>{{ old('diagnosis') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="medications" class="font-weight-bold">Medications</label>
                                <textarea id="medications" class="form-control" name="medications" rows="3" required>{{ old('medications') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="treatments" class="font-weight-bold">Treatments</label>
                                <textarea id="treatments" class="form-control" name="treatments" rows="3" required>{{ old('treatments') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save Record') }}</button>
                </div>
            </form>

            </div>
        </div>
    </div>
</div>
@endforeach


<!-- Confirmation Modal -->
@foreach($appointments as $appointment)
<div class="modal fade" id="confirmationModal_{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to update the appointment status?
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary confirmUpdate" data-appointment-id="{{ $appointment->id }}">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $('.confirmUpdate').click(function() {
        var appointmentId = $(this).data('appointment-id');
        $('#updateForm_' + appointmentId).submit();
    });

    function confirmStatusUpdate(appointmentId, status) {
        $('#updateForm_' + appointmentId).append('<input type="hidden" name="status" value="' + status + '">');
        $('#confirmationModal_' + appointmentId).modal('show');
    }
    $(document).ready(function() {
    $('.addRecordBtn').click(function() {
        var appointmentId = $(this).data('appointment-id');
        var appointmentDate = $(this).data('appointment-date');

        // Format appointmentDate as YYYY-MM-DD for input type="date"
        var formattedDate = new Date(appointmentDate).toISOString().split('T')[0];

        // Use the dynamically generated modal ID
        $('#addRecordModal_' + appointmentId).modal('show');

        // Populate the appointment ID and formatted date in the form fields
        $('#appointment_id').val(appointmentId);
        $('#record_date').val(formattedDate);
    });
});


</script>

@endsection
