@extends('layouts.Patient.navbar')

@section('content')
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="table-responsive">
            <table id="myTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th colspan="6">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6">
                                    <h1 class="h2">My Appointments</h1>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('patient.slots') }}" class="btn btn-primary">Book New Appointment</a>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-center">Slot Number</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Appointment Type</th>
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
                            <td class="text-center">{{ date('F d, Y', strtotime($appointment->slot->bookingLimit->date)) }}</td>
                            <td class="text-center">{{ date('h:i A', strtotime($appointment->slot->start_time)) }} - {{ date('h:i A', strtotime($appointment->slot->end_time)) }}</td>
                            <td class="text-center">{{ $appointment->type }}</td>
                            <td id="statusCell{{ $appointment->id }}" class="text-center">
                                <span class="@if($appointment->status == 'booked') bg-primary text-white @elseif($appointment->status == 'completed') bg-success text-white @elseif($appointment->status == 'no show') bg-secondary text-white @elseif($appointment->status == 'cancelled') bg-danger text-white @elseif($appointment->status == 'reschedule') bg-warning text-white @endif px-2 rounded">{{ ucfirst($appointment->status) }}</span>
                            </td>
                            <td class="text-center">
                                @if($appointment->status == 'booked')
                                    <div class="btn-group">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $appointment->id }}">Reschedule</button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $appointment->id }}">Cancel</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Reschedule Modal -->
                        <div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="rescheduleModalLabel{{ $appointment->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rescheduleModalLabel{{ $appointment->id }}">Reschedule Appointment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('appointments.reschedule', ['id' => $appointment->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            Are you sure you want to reschedule this appointment?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Reschedule</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Cancel Modal -->
                        <div class="modal fade" id="cancelModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $appointment->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelModalLabel{{ $appointment->id }}">Cancel Appointment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('appointments.cancel', ['id' => $appointment->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            Are you sure you want to cancel this appointment?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#myTable').DataTable();
        });
    </script>
   @if(Session::has('reschedule_success'))
    <script>
        swal("Action completed successfully!","{{ Session::get('reschedule_success') }}","success",{
            button: "OK",
        });
    </script>
    @endif
    
    @if(Session::has('cancel_success'))
    <script>
        swal("Action completed successfully!","{{ Session::get('cancel_success') }}","success",{
            button: "OK",
        });
    </script>
    @endif
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
