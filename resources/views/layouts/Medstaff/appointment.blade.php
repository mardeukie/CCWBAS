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
                                <h1 class="h2">Appointments</h1>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('medstaff.calendar') }}" class="btn btn-primary btn-sm">Add Appointment</a>
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
            @foreach($appointments->sortByDesc('created_at') as $appointment)
                    @php
                        $slot = $appointment->slot;
                        $previousAppointments = $slot->appointments->where('created_at', '<', $appointment->created_at);
                        $previousAppointmentsCount = $previousAppointments->count();
                        $orderNumber = $previousAppointmentsCount + 1; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $orderNumber }}</td>
                        <td class="text-center">{{ date('M d, Y', strtotime($appointment->slot->bookingLimit->date)) }}</td>
                        <td class="text-center">
                            @if($appointment->patient)
                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-center">{{ $appointment->type }}</td>
                        <td class="text-center">{{ $appointment->complaints->isEmpty() ? 'No details available' : $appointment->complaints->first()->details }}</td>
                        <td id="statusCell{{ $appointment->id }}" class="text-center">
                            <span class="@if($appointment->status == 'booked') bg-primary text-white @elseif($appointment->status == 'completed') bg-success text-white @elseif($appointment->status == 'no show') bg-secondary text-white @elseif($appointment->status == 'cancelled') bg-danger text-white @elseif($appointment->status == 'reschedule') bg-warning text-white @endif px-2 rounded">{{ ucfirst($appointment->status) }}</span>
                        </td>
                        <td class="text-center">
                            @if($appointment->status == 'booked')
                                <form id="updateForm_{{ $appointment->id }}" method="POST" action="{{ route('appointments.update-status', $appointment) }}">
                                    @csrf
                                    <div class="btn-group" role="group" aria-label="Appointment actions">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmStatusUpdate('{{ $appointment->id }}', 'cancelled')"><i class="fas fa-times-circle"></i></button>
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
</script>

<style>
    .bg-primary {
        background-color: blue;
    }

    .bg-success {
        background-color: green;
    }

    .bg-secondary {
        background-color: gray;
    }

    .bg-danger {
        background-color: red;
    }

    .bg-warning {
        background-color: yellow;
    }

    .text-white {
        color: white;
    }

    .text-center {
        text-align: center;
    }
</style>

@endsection
