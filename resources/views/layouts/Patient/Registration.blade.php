<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.1.2/tailwind.min.css">
    <script>
        function validateForm() {
            var firstName = document.getElementById('first_name').value.trim();
            var lastName = document.getElementById('last_name').value.trim();
            var contactNumber = document.getElementById('contact_number').value.trim();
            var dateOfBirth = document.getElementById('date_of_birth').value.trim();
            
            // Basic validation: Ensure required fields are not empty
            if (firstName === '' || lastName === '' || contactNumber === '' || dateOfBirth === '') {
                alert('Please fill out all required fields.');
                return false;
            }
            return true;
        }
    </script>
</head>

<body style="background-color: #007AFF;">

    <section class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-2xl">
            <div class="bg-white shadow-md rounded-lg px-8 py-6 mx-4">
                <h2 class="text-center text-2xl font-bold mb-6 text-gray-800">{{ __('Patient Registration') }}</h2>
                <form method="POST" action="{{ route('patient') }}" onsubmit="return validateForm()">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <!-- First Name Input -->
                        <div class="mb-4">
                            <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                            <input id="first_name" type="text"
                                class="input-field @error('first_name') border-red-500 @enderror" name="first_name"
                                value="{{ old('first_name') }}" required autocomplete="first_name">
                            @error('first_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name Input -->
                        <div class="mb-4">
                            <label for="middle_name" class="block text-gray-700 text-sm font-bold mb-2">Middle Name</label>
                            <input id="middle_name" type="text"
                                class="input-field @error('middle_name') border-red-500 @enderror" name="middle_name"
                                value="{{ old('middle_name') }}" autocomplete="middle_name">
                            @error('middle_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name Input -->
                        <div class="mb-4">
                            <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                            <input id="last_name" type="text"
                                class="input-field @error('last_name') border-red-500 @enderror" name="last_name"
                                value="{{ old('last_name') }}" required autocomplete="last_name">
                            @error('last_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Number Input -->
                        <div class="mb-4">
                            <label for="contact_number" class="block text-gray-700 text-sm font-bold mb-2">Contact
                                Number</label>
                            <input id="contact_number" type="text"
                                class="input-field @error('contact_number') border-red-500 @enderror" name="contact_number"
                                value="{{ old('contact_number') }}" required autocomplete="contact_number">
                            @error('contact_number')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth Input -->
                        <div class="mb-4">
                            <label for="date_of_birth" class="block text-gray-700 text-sm font-bold mb-2">Date of
                                Birth</label>
                            <input id="date_of_birth" type="date"
                                class="input-field @error('date_of_birth') border-red-500 @enderror" name="date_of_birth"
                                value="{{ old('date_of_birth') }}" required autocomplete="date_of_birth">
                            @error('date_of_birth')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender Input -->
                        <div class="mb-4">
                            <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Gender</label>
                            <select id="gender" name="gender"
                                class="input-field @error('gender') border-red-500 @enderror">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                         <!--Province-->
                         <div class="mb-4">
                            <label for="province" class="block text-gray-700 text-sm font-bold mb-2">Province</label>
                            <select id="province" name="province" class="input-field @error('province') border-red-500 @enderror" required>
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

                        <!-- Municipality Input -->
                        <div class="mb-4">
                            <label for="municipality" class="block text-gray-700 text-sm font-bold mb-2">Municipality</label>
                            <select id="municipality" name="municipality"
                                class="input-field @error('municipality') border-red-500 @enderror" required>
                                <option value="" disabled selected>Select Municipality</option>
                                <!-- Options will be dynamically populated based on the selected province -->
                            </select>
                            <input type="hidden" name="municipality_id" id="municipality_id" value="">
                            @error('municipality')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barangay Input -->
                        <div class="mb-4">
                            <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">Barangay</label>
                            <select id="barangay" name="barangay"
                                class="input-field @error('barangay') border-red-500 @enderror" required>
                                <option value="" disabled selected>Select Barangay</option>
                                <!-- Options will be dynamically populated based on the selected municipality -->
                            </select>
                            <input type="hidden" name="barangay_id" id="barangay_id" value="">
                            @error('barangay')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Input -->
                        <div class="mb-4">
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

                    <div class="flex items-center justify-center mt-6">
                        <button type="submit"
                            class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            {{ __('Register') }}
                        </button>
                    </div>
                </form>
                
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#province').on('change', function () {
                var provinceId = this.value;
                $('#province_id').val(provinceId);
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
                        console.log(res);
                        $('#municipality').html('<option value="">Select Municipality</option>');
                        if (res.length > 0) {
                            $.each(res, function (key, value) {
                                $('#municipality').append('<option value="' + value.id + '">' + value.municipality + '</option>');
                            });
                        } else {
                            $('#municipality').append('<option value="">No municipalities found</option>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#municipality').on('change', function () {
                var municipalityId = this.value;
                $('#municipality_id').val(municipalityId);
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
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('form').submit(function () {
                $('#province_id').val($('#province').val());
                $('#municipality_id').val($('#municipality').val());
                $('#barangay_id').val($('#barangay').val());
            });
        });
    </script>


</body>

</html>
