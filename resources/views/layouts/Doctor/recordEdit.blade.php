@extends('layouts.Doctor.navbar')

@section('content')
<div class="container">
    <h1>Edit Patient Records</h1>
    <div class="my-3">
        <a href="{{ route('doctor.records') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="row">
        <!-- Personal Information Editing Section -->
        <div class="col-md-5">
            <section id="personal-info" class="border p-3 mb-3">
                <h2>Personal Information</h2>
                <form method="POST" action="{{ route('doctor.patientUpdate', $patient->id) }}">
                    @csrf
                    @method('PATCH')
                    <!-- Personal info fields go here -->
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $patient->first_name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $patient->middle_name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $patient->last_name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number{{ $patient->id }}" class="font-weight-bold">Contact Number:</label>
                        <input id="contact_number{{ $patient->id }}" type="text" class="form-control" name="contact_number" value="{{ $patient->contact_number }}" required>
                    </div>
                    <div class="form-group">
                        <label for="gender{{ $patient->id }}" class="font-weight-bold">Gender:</label>
                            <select id="gender{{ $patient->id }}" class="form-control" name="gender" required>
                                <option value="Male" {{ $patient->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $patient->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $patient->gender === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth{{ $patient->id }}" class="font-weight-bold">Date of Birth:</label>
                        <input id="date_of_birth{{ $patient->id }}" type="date" class="form-control" name="date_of_birth" value="{{ $patient->date_of_birth->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="status{{ $patient->id }}" class="font-weight-bold">Civil Status:</label>
                            <select id="status{{ $patient->id }}" class="form-control" name="status" required>
                                <option value="Single" {{ $patient->status === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ $patient->status === 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Annulled" {{ $patient->status === 'Annulled' ? 'selected' : '' }}>Annulled</option>
                                <option value="Widowed" {{ $patient->status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ $patient->status === 'Separated' ? 'selected' : '' }}>Separated</option>
                                <option value="Divorced" {{ $patient->status === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="province{{ $patient->id }}" class="block text-gray-700 text-sm font-bold mb-2">Province</label>
                        <select id="province{{ $patient->id }}" name="province" class="form-control @error('province') border-red-500 @enderror" required>
                            <option disabled>Select Province</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}" {{ $province->id == $patient->province_id ? 'selected' : '' }}>{{ $province->province }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="province_id" id="province_id{{ $patient->id }}" value="{{ $patient->province_id }}">
                        @error('province')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="municipality{{ $patient->id }}" class="block text-gray-700 text-sm font-bold mb-2">Municipality</label>
                        <select id="municipality{{ $patient->id }}" name="municipality" class="form-control @error('municipality') border-red-500 @enderror" required>
                            <option value="" disabled>Select Municipality</option>
                            @foreach ($municipalities as $municipality)
                                <option value="{{ $municipality->id }}" {{ $municipality->id == $patient->municipality_id ? 'selected' : '' }}>{{ $municipality->municipality }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="municipality_id" id="municipality_id{{ $patient->id }}" value="{{ $patient->municipality_id }}">
                        @error('municipality')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="barangay{{ $patient->id }}" class="block text-gray-700 text-sm font-bold mb-2">Barangay</label>
                        <select id="barangay{{ $patient->id }}" name="barangay" class="form-control @error('barangay') border-red-500 @enderror" required>
                            <option value="" disabled>Select Barangay</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ $barangay->id == $patient->barangay_id ? 'selected' : '' }}>{{ $barangay->barangay }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="barangay_id" id="barangay_id{{ $patient->id }}" value="{{ $patient->barangay_id }}">
                        @error('barangay')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </section>
        </div>


        <!-- Medical History Section -->
        <div class="col-md-7">
            <section id="medical-history" class="border p-3 mb-3">
                <h2>Medical History</h2>
                <div class="table-responsive">
                    <table class="table table-striped" id="editableTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Appointment Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patient->records->sortBy('date') as $record)
                            <tr>
                                <td>{{ $record->date }}</td>
                                <td>{{ $record->appointment->type }}</td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#editRecordModal{{ $record->id }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteRecordModal{{ $record->id }}">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Edit Record Modal -->
        @foreach ($patient->records as $record)
        <div class="modal fade" id="editRecordModal{{ $record->id }}" tabindex="-1" role="dialog" aria-labelledby="editRecordModal{{ $record->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRecordModal{{ $record->id }}Label">Edit Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('patient.recordUpdate', ['id' => $record->id]) }}">
                            @csrf
                            @method('PATCH')
                            <!-- Decode the vital signs JSON string -->
                            @php
                            $vitalSigns = json_decode($record->vital_signs, true);
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ $record->date }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="appointment_type">Appointment Type</label>
                                        <input type="text" class="form-control" id="appointment_type" name="appointment_type" value="{{ $record->appointment->type }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="complaint_details">Complaint Details</label>
                                        <textarea class="form-control" id="complaint_details" name="complaint_details" rows="2" required>
                                            @foreach ($record->appointment->complaints as $complaint)
                                                {{ $complaint->details }}
                                            @endforeach
                                        </textarea>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label for="blood_pressure">Blood Pressure</label>
                                            <input type="text" class="form-control" id="blood_pressure" name="blood_pressure" value="{{ $vitalSigns['blood_pressure'] }}" required>
                                        </div>
                                        <div class="col">
                                            <label for="heart_rate">Heart Rate</label>
                                            <input type="text" class="form-control" id="heart_rate" name="heart_rate" value="{{ $vitalSigns['heart_rate'] }}" required>
                                        </div>
                                        <div class="col">
                                            <label for="temperature">Temperature</label>
                                            <input type="text" class="form-control" id="temperature" name="temperature" value="{{ $vitalSigns['temperature'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label for="height">Height</label>
                                            <input type="text" class="form-control" id="height" name="height" value="{{ $vitalSigns['height'] }}" required>
                                        </div>
                                        <div class="col">
                                            <label for="weight">Weight</label>
                                            <input type="text" class="form-control" id="weight" name="weight" value="{{ $vitalSigns['weight'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" required>{{ $record->notes }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="diagnosis">Diagnosis</label>
                                        <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required>{{ $record->diagnosis }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="treatments">Treatments</label>
                                        <textarea class="form-control" id="treatments" name="treatments" rows="3" required>{{ $record->treatments }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="medications">Medications</label>
                                        <textarea class="form-control" id="medications" name="medications" rows="3" required>{{ $record->medications }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="referral">Referral</label>
                                        <textarea class="form-control" id="referral" name="referral" rows="3" required>{{ $record->referral }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Delete Record Modal -->
        @foreach ($patient->records as $record)
        <div class="modal fade" id="deleteRecordModal{{ $record->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteRecordModal{{ $record->id }}Label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRecordModal{{ $record->id }}Label">Delete Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this record?</p>
                        <form method="POST" action="{{ route('destroy.records', ['id' => $record->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
    // Event listener for the change event on province select elements
        $('[id^=province]').on('change', function () {
            var provinceId = $(this).val();
            var municipalitySelect = $(this).closest('form').find('[id^=municipality]');
            var provinceHiddenInput = $(this).closest('form').find('[name^=province_id]');
            populateMunicipalities(provinceId, municipalitySelect);
            provinceHiddenInput.val(provinceId); 
        });

        // Event listener for the change event on municipality select elements
        $('[id^=municipality]').on('change', function () {
            var municipalityId = $(this).val();
            var barangaySelect = $(this).closest('form').find('[id^=barangay]');
            var municipalityHiddenInput = $(this).closest('form').find('[name^=municipality_id]');
            populateBarangays(municipalityId, barangaySelect);
            municipalityHiddenInput.val(municipalityId); 
        });

        // Event listener for the change event on barangay select elements
        $('[id^=barangay]').on('change', function () {
            var barangayId = $(this).val();
            var barangayHiddenInput = $(this).closest('form').find('[name^=barangay_id]');
            barangayHiddenInput.val(barangayId); 
        });

        // Function to populate municipalities based on the selected province
        function populateMunicipalities(provinceId, municipalitySelect) {
            $.ajax({
                url: '{{ route('patient.municipalities') }}',
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
                url: '{{ route('patient.barangays') }}',
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
                    var selectedBarangayId = $(barangaySelect).val();
                    var barangayHiddenInput = $(barangaySelect).closest('form').find('[name^=barangay_id]');
                    barangayHiddenInput.val(selectedBarangayId); 
                } else {
                    $(barangaySelect).append('<option value="">No barangays found</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
            });
        }

        // Initial population of municipalities and barangays based on the selected province and municipality
        $('[id^=province]').each(function () {
            var provinceId = $(this).val();
            var municipalitySelect = $(this).closest('form').find('[id^=municipality]');
            populateMunicipalities(provinceId, municipalitySelect);
        });

        $('[id^=municipality]').each(function () {
            var municipalityId = $(this).val();
            var barangaySelect = $(this).closest('form').find('[id^=barangay]');
            populateBarangays(municipalityId, barangaySelect);
        });
    });
    </script>
@endsection
