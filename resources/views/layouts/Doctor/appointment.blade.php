@extends('layouts.Doctor.navbar')

@section('content')
<div class="container">
    <div class="table-responsive">
        <table id="myData" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th colspan="6">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Appointments</h1>
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
                        <td class="text-center">
                            @if($appointment->status == 'cancelled')
                                <span class="badge badge-danger text-white">{{ $appointment->status }}</span>
                            @elseif($appointment->status == 'completed')
                                <span class="badge badge-success">{{ $appointment->status }}</span>
                            @elseif($appointment->status == 'no show')
                                <span class="badge badge-secondary">{{ $appointment->status }}</span>
                            @elseif($appointment->status == 'rescheduled')
                                <span class="badge badge-warning text-dark">{{ $appointment->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

@endsection
