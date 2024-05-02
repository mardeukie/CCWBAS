@extends('layouts.Medstaff.navbar')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="table-responsive">
        <table id="myDataTable" class="table table-bordered table-striped">
            <!-- Table Headers -->
            <thead>
                <tr>
                    <th colspan="5">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Appointment Slots</h1>
                            </div>
                            <div class="col-md-6 text-right">
                                <button id="updateSlotStatusBtn" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#confirmationModal">Update Slot Status</button>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createBookingModal">Generate Slot</button>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">Start Time</th>
                    <th class="text-center">End Time</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <!-- Table Body -->
            <tbody>
                @forelse($slots as $slot)
                    <tr>
                        <td class="text-center">{{ date('F j, Y', strtotime($slot->bookingLimit->date)) }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time)->format('h:i A') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->end_time)->format('h:i A') }}</td>
                        <td class="text-center">{{ $slot->status }}</td>
                        <td class="text-center">
                            @if ($slot->status === 'available')
                                <a href="#" data-toggle="modal" data-target="#editSlotModal{{ $slot->id }}">
                                    <i class="fas fa-edit text-secondary" title="Edit"></i>
                                </a>
                            @endif
                            <a href="#" data-toggle="modal" data-target="#destroySlotModal{{ $slot->id }}">
                                <i class="fas fa-trash text-danger" title="Delete"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No slots available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <!--create modal-->
    <div id="createBookingModal" class="modal" style="display: none;">
        <form method="post" action="{{ route('create.booking') }}" id="createBookingForm">
            @csrf
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Slots</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Booking Limit Fields -->
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control datepicker" name="date" placeholder="Date" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="slot_number">Number of Slots</label>
                            <input type="text" class="form-control" name="slot_number" placeholder="Number of Slots">
                        </div>

                        <!-- Slot Fields -->
                        <div class="form-group">
                            <label for="start_time">Start Time</label>
                            <input type="time" class="form-control" name="start_time" placeholder="Start Time">
                        </div>
                        <div class="form-group">
                            <label for="end_time">End Time</label>
                            <input type="time" class="form-control" name="end_time" placeholder="End Time">
                        </div>
                        <div class="form-group">
                            <label for="from">From</label>
                            <input type="date" class="form-control datepicker" name="from" placeholder="From" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to">To</label>
                            <input type="date" class="form-control datepicker" name="to" placeholder="To" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Generate Slots</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--Edit Slot modal-->
    @foreach($slots as $slot)
        <div id="editSlotModal{{ $slot->id }}" class="modal" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Slot</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('medstaff.update.slot', ['id' => $slot->id]) }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control datepicker" name="date" value="{{ $slot->bookingLimit->date }}" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="slot_number">Number of Slots</label>
                                <input type="text" class="form-control" name="slot_number" value="{{ $slot->bookingLimit->slot_number }}">
                            </div>
                            <!-- Slot Fields -->
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input type="time" class="form-control" name="start_time" value="{{ $slot->start_time }}">
                            </div>
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="time" class="form-control" name="end_time" value="{{ $slot->end_time }}">
                            </div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Delete Slot Modal -->
    @foreach($slots as $slot)
        <div id="destroySlotModal{{ $slot->id }}" class="modal" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Slot</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this slot?</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" action="{{ route('medstaff.destroy.slot', ['id' => $slot->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Slot</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Update Slot Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to update the slot status?
            </div>
            <div class="modal-footer">
                <form id="updateSlotStatusForm" method="post" action="{{ route('update.slot.status') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script>
    $(document).ready(function () {
        // Display SweetAlert after successful booking slot creation
        @if(session('success'))
            swal("Action Completed Successfully!", "{{ session('success') }}", "success");
        @endif

        // Datepicker initialization
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true
        });

        // Show createBookingModal on page load
        $('#createBookingModal').modal('show');

        // Handle form submission for creating booking
        $('#createBookingForm').submit(function (event) {
            event.preventDefault();

            var formData = $(this).serializeArray();
            formData.push({ name: 'to', value: $('input[name="to"]').val() });
            $.ajax({
                url: '{{ route('create.booking') }}',
                method: 'POST',
                data: formData,
                success: function (response) {
                    console.log(response); // Check the response in the console
                    // Hide the createBookingModal
                    $('#createBookingModal').modal('hide');

                    // Display SweetAlert on success
                    swal("Action Completed Successfully!", "Booking slots generated successfully", "success");
                },
                error: function (error) {
                    // Handle errors
                    console.error('Error:', error);
                }
            });
        });
    });
</script>



@endsection