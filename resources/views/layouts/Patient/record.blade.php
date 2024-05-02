@extends('layouts.Patient.navbar')

@section('content')
<div class="container-lg shadow-lg p-3 mb-5 bg-white rounded">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="section-title">Personal Information</h2>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#editPersonalInfoModal">Edit</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="patient-info">
                                <p><strong>Name:</strong> {{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</p>
                                <p><strong>Gender:</strong> {{ $patient->gender }}</p>
                                <p><strong>Date of Birth:</strong> {{ $patient->date_of_birth->format('F j, Y') }}</p>
                                <p><strong>Contact Number:</strong> {{ $patient->contact_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="patient-info">
                                <p><strong>Address:</strong> {{ $patient->barangay->barangay }}, {{ $patient->municipality->municipality }}, {{ $patient->province->province }}</p>
                                <p><strong>Civil Status:</strong> {{ $patient->status }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                View Medical Records
            </button>
            <div class="collapse mt-4" id="collapseExample">
                <div class="card-header">
                    <h2 class="section-title">Medical Records</h2>
                </div>
                <div class=" card card-body">
                    <div class="records-table">
                        @if($records->isEmpty())
                            <p>No records found for this patient.</p>
                        @else
                            <table id="myTable" class="table table-bordered table-striped">
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
                                @foreach($records->sortByDesc('date') as $record)
                                    <tr>
                                        <td>{{ date('F j, Y', strtotime($record->date)) }}</td>
                                        <td>{{ $record->appointment->type }}</td>
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Personal Information Modal -->
<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" role="dialog" aria-labelledby="editPersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonalInfoModalLabel">Edit Personal Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for editing personal information -->
                <form method="POST" action="{{ route('patients.updateInfo', $patient->id) }}">
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
            </div>
        </div>
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
                url: '{{ route('municipalities.get') }}',
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
                url: '{{ route('barangays.get') }}',
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    @if(Session::has('success'))
        <script>
            swal("Edited successfully!","{!! Session::get('success') !!}","success",{
                button: "OK",
            });
        </script>
    @endif
@endsection
