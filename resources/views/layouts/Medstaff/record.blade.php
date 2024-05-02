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
                                <h1 class="h2">Patient Medical Record</h1>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#addPatientModal" class="btn btn-primary btn-sm" data-toggle="modal">Add Patient</a>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Age</th>
                    <th class="text-center">Gender</th>
                    <th class="text-center">Address</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <!-- Table Body -->
            <tbody>
                @foreach ($patients as $patient)
                <tr>
                    <td class="text-center">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                    <td class="text-center">{{ $patient->date_of_birth->age }}</td>
                    <td class="text-center">{{ $patient->gender }}</td>
                    <td class="text-center">{{ $patient->barangay->barangay }}, {{ $patient->municipality->municipality }}, {{ $patient->province->province }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="#" data-toggle="modal" data-target="#viewPatientModal{{ $patient->id }}">
                                <i class="fas fa-eye text-primary mr-1" title="View"></i>
                            </a>
                            <a href="{{ route('patients.edit', ['id' => $patient->id]) }}">
                                <i class="fas fa-edit text-secondary mr-1" title="Edit"></i>
                            </a>
                            <a href="#" data-toggle="modal" data-target="#deletePatientModal{{ $patient->id }}">
                                <i class="fas fa-trash text-danger" title="Delete"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

 <!-- Add Patient Modal -->
 <div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h2 class="text-center text-2xl font-bold mb-6 text-gray-800">{{ __('Patient Record') }}</h2>
                    <form method="POST" action="{{ route('patient.registration.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <!-- First Name Input -->
                                <div class="form-group">
                                    <label for="first_name" class="font-weight-bold">First Name</label>
                                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name">
                                </div>
                                <!-- Middle Name Input -->
                                <div class="form-group">
                                    <label for="middle_name" class="font-weight-bold">Middle Name</label>
                                    <input id="middle_name" type="text" class="form-control" name="middle_name" value="{{ old('middle_name') }}" autocomplete="middle_name">
                                </div>
                                <!-- Last Name Input -->
                                <div class="form-group">
                                    <label for="last_name" class="font-weight-bold">Last Name</label>
                                    <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name">
                                </div>
                                <!-- Date of Birth Input -->
                                <div class="form-group">
                                    <label for="date_of_birth" class="font-weight-bold">Date of Birth</label>
                                    <input id="date_of_birth" type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="date_of_birth">
                                </div>
                                <!-- Gender Input -->
                                <div class="form-group">
                                    <label for="gender" class="font-weight-bold">Gender</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="font-weight-bold">Email</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Contact Number Input -->
                                <div class="form-group">
                                    <label for="contact_number" class="font-weight-bold">Contact Number</label>
                                    <input id="contact_number" type="text" class="form-control" name="contact_number" value="{{ old('contact_number') }}" required autocomplete="contact_number">
                                </div>
                                    <div class="form-group">
                                        <label for="province" class="block text-gray-700 text-sm font-bold mb-2">Province</label>
                                        <select id="province" name="province" class="form-control @error('province') border-red-500 @enderror" required>
                                            <option selected disabled>Select Province</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}">{{ $province->province }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="province_id" id="province_id" value="">
                                        @error('province')
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="municipality" class="block text-gray-700 text-sm font-bold mb-2">Municipality</label>
                                        <select id="municipality" name="municipality" class="form-control @error('municipality') border-red-500 @enderror" required>
                                            <option value="" disabled selected>Select Municipality</option>
                                        </select>
                                        <input type="hidden" name="municipality_id" id="municipality_id" value="">
                                        @error('municipality')
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">Barangay</label>
                                        <select id="barangay" name="barangay" class="form-control @error('barangay') border-red-500 @enderror" required>
                                            <option value="" disabled selected>Select Barangay</option>
                                        </select>
                                        <input type="hidden" name="barangay_id" id="barangay_id" value="">
                                        @error('barangay')
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Status Input -->
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Civil Status</label>
                                        <select id="status" name="status" class="form-control" required>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Annulled">Anulled</option>
                                            <option value="Widowed">Widowed</option>
                                            <option value="Separated">Legally Separated</option>
                                            <option value="Divorced">Divorced</option>
                                        </select>
                                    </div>
                            </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Register') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Patient Modal -->
@foreach ($patients as $patient)
<div class="modal fade" id="viewPatientModal{{ $patient->id }}" tabindex="-1" role="dialog" aria-labelledby="viewPatientModalLabel{{ $patient->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body" id="viewPatientModalContent{{ $patient->id }}">
                <h2 class="text-center text-2xl font-bold mb-6 text-gray-800">{{ __('Patient Information') }}</h2>

                <!-- Personal Information Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="font-weight-bold">First Name:</label>
                        <span>{{ $patient->first_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Middle Name:</label>
                        <span>{{ $patient->middle_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Last Name:</label>
                        <span>{{ $patient->last_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Gender:</label>
                        <span>{{ $patient->gender}}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Date of Birth:</label>
                        <span>{{ $patient->date_of_birth->format('F j, Y') }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Contact Number:</label>
                        <span>{{ $patient->contact_number }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Civil Status:</label>
                        <span>{{ $patient->status }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Address:</label>
                        <span>
                            @if($patient->barangay)
                                {{ $patient->barangay->barangay }},
                            @else
                                N/A,
                            @endif
                            @if($patient->municipality)
                                {{ $patient->municipality->municipality }},
                            @else
                                N/A,
                            @endif
                            @if($patient->province)
                                {{ $patient->province->province }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Medical History Section -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Appointment Type</th>
                                <th>Complaint</th>
                                <th>Vital Signs</th>
                                <th>Diagnosis</th>
                                <th>Treatments</th>
                                <th>Medications</th>
                                <th>Referral</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patient->records->sortByDesc('date') as $record)
                            <tr>
                                <td>{{ date('F j, Y', strtotime($record->date)) }}</td>
                                <td>
                                    @if ($record->appointment)
                                    {{ $record->appointment->type }}
                                    @else
                                    No appointment
                                    @endif
                                </td>
                                <td>
                                    @foreach ($record->appointment->complaints as $complaint)
                                        {{ $complaint->details }}
                                    @endforeach
                                </td>
                                <td>
                                    @foreach(json_decode($record->vital_signs) as $key => $vitalSign)
                                        <p><strong>{{ $key }}:</strong> {{ $vitalSign }}</p>
                                    @endforeach
                                </td>
                                <td>{{ $record->diagnosis }}</td>
                                <td>{{ $record->treatments }}</td>
                                <td>{{ $record->medications }}</td>
                                <td>{{ $record->referral }}</td>
                                <td>{{ $record->notes }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <button onclick="printViewModal('viewPatientModalContent{{ $patient->id }}')" type="button" class="btn btn-primary print-ignore">Print</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Delete Patient Modal -->
@foreach ($patients as $patient)
<div class="modal fade" id="deletePatientModal{{ $patient->id }}" tabindex="-1" role="dialog" aria-labelledby="deletePatientModalLabel{{ $patient->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePatientModalLabel{{ $patient->id }}">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the patient record for {{ $patient->first_name }} {{ $patient->last_name }}?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('patients.delete', ['id' => $patient->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

<script type="text/javascript">
$(document).ready(function () {
    $('#addPatientModal').on('shown.bs.modal', function () {
        $('#province').on('change', function () {
            var provinceId = this.value;
            $('#municipality').html('');
            $('#barangay').html('');
            $.ajax({
                url: '{{ route('municipalities') }}',
                type: 'get',
                data: { province_id: provinceId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#municipality').html('<option value="">Select Municipality</option>');
                    if (res.length > 0) {
                        $.each(res, function (key, value) {
                            $('#municipality').append('<option value="' + value.id + '">' + value.municipality + '</option>');
                        });
                    } else {
                        $('#municipality').append('<option value="">No municipalities found</option>');
                    }
                    $('#province_id').val(provinceId); 
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#municipality').on('change', function () {
            var municipalityId = this.value;
            $('#barangay').html('');
            $.ajax({
                url: '{{ route('barangays') }}',
                type: 'get',
                data: { municipality_id: municipalityId },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#barangay').html('<option value="">Select Barangay</option>');
                    if (res.length > 0) {
                        $.each(res, function (key, value) {
                            $('#barangay').append('<option value="' + value.id + '">' + value.barangay + '</option>');
                        });
                    } else {
                        $('#barangay').append('<option value="">No barangays found</option>');
                    }
                    $('#municipality_id').val(municipalityId);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('form').submit(function () {
            $('#barangay_id').val($('#barangay').val()); 
        });
    });
    // Function to populate municipalities based on the selected province
    function populateMunicipalities(provinceId, municipalitySelect) {
        $.ajax({
            url: '{{ route('municipalities') }}',
            type: 'get',
            data: { province_id: provinceId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $(municipalitySelect).html('<option value="">Select Municipality</option>');
                if (res.length > 0) {
                    $.each(res, function (key, value) {
                        $(municipalitySelect).append('<option value="' + value.id + '">' + value.municipality + '</option>');
                    });
                } else {
                    $(municipalitySelect).append('<option value="">No municipalities found</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Function to populate barangays based on the selected municipality
    function populateBarangays(municipalityId, barangaySelect) {
        $.ajax({
            url: '{{ route('barangays') }}',
            type: 'get',
            data: { municipality_id: municipalityId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                $(barangaySelect).html('<option value="">Select Barangay</option>');
                if (res.length > 0) {
                    $.each(res, function (key, value) {
                        $(barangaySelect).append('<option value="' + value.id + '">' + value.barangay + '</option>');
                    });
                } else {
                    $(barangaySelect).append('<option value="">No barangays found</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Event listener for the change event on province select elements
    $('[id^=province]').on('change', function () {
        var provinceId = $(this).val();
        var municipalitySelect = $(this).closest('.col-md-6').find('[id^=municipality]');
        populateMunicipalities(provinceId, municipalitySelect);
    });

    // Event listener for the change event on municipality select elements
    $('[id^=municipality]').on('change', function () {
        var municipalityId = $(this).val();
        var barangaySelect = $(this).closest('.col-md-6').find('[id^=barangay]');
        populateBarangays(municipalityId, barangaySelect);
    });

    // Initial population of municipalities and barangays based on the selected province and municipality
    $('[id^=province]').each(function () {
        var provinceId = $(this).val();
        var municipalitySelect = $(this).closest('.col-md-6').find('[id^=municipality]');
        populateMunicipalities(provinceId, municipalitySelect);
    });

    $('[id^=municipality]').each(function () {
        var municipalityId = $(this).val();
        var barangaySelect = $(this).closest('.col-md-6').find('[id^=barangay]');
        populateBarangays(municipalityId, barangaySelect);
    });
    $('[id^=deletePatientModal]').on('click', function () {
        var patientId = $(this).data('patient-id');
        swal({
            title: "Confirm Deletion",
            text: "Are you sure you want to delete the patient record?",
            icon: "warning",
            buttons: ["Cancel", "Delete"],
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $('#deletePatientForm' + patientId).submit();
            }
        });
    });
});
function printViewModal(contentId) {
    var printContents = document.getElementById(contentId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>
@if(Session::has('success'))
    <script>
        swal("Action Completed Successfully!","{!! Session::get('success') !!}","success",{
            button: "OK",
        });
    </script>
@endif
<style>
    @media print {
    .print-ignore,
    .print-ignore * {
        display: none !important;
    }
}
</style>


@endsection
