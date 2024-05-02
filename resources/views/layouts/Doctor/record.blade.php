@extends('layouts.Doctor.navbar')

@section('content')
    <div class="container">
    <div class="table-responsive">
        <table id="myData" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th colspan="5">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-6">
                                <h1 class="h2">Patient Medical Records</h1>
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
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewPatientModal{{ $patient->id }}">View</button>
                            <a href="{{ route('doctor.patientEdit', ['id' => $patient->id]) }}" class="btn btn-primary">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deletePatientModal{{ $patient->id }}">Delete</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
                            @foreach ($patient->records->sortBy('date') as $record)
                            <tr>
                                <td>{{ $record->date }}</td>
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
                <form method="POST" action="{{ route('records.deletePatient', ['id' => $patient->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach


<script type="text/javascript">
    function printViewModal(contentId) {
    var printContents = document.getElementById(contentId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
$(document).ready(function () {
    $('#addPatientModal').on('shown.bs.modal', function () {
        $('#province').on('change', function () {
            var provinceId = this.value;
            $('#municipality').html('');
            $('#barangay').html('');
            $.ajax({
                url: '{{ route('patient.municipalities') }}',
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
                url: '{{ route('patient.barangays') }}',
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
});

</script>
<style>
    @media print {
    .print-ignore,
    .print-ignore * {
        display: none !important;
    }
}
</style>

@endsection
