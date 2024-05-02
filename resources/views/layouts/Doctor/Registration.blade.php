<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.1.2/tailwind.min.css">
</head>
<body style="background-color: #007AFF;">
<section>
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-opacity-50 backdrop-filter backdrop-blur-sm"></div>
        <div class="relative z-10 flex justify-center items-center min-h-screen">
            <div class="w-full max-w-xl">
                <div class="bg-white shadow-md rounded-lg px-8 py-6">
                    <h2 class="text-center text-2xl font-bold mb-6">{{ __('Doctor Registration') }}</h2>
                    <form method="POST" action="{{ route('doctor') }}">
                        @csrf

                        <!-- First Name Input -->
                        <div class="mb-6">
                            <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('First Name') }}</label>
                            <input id="first_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('first_name') border-red-500 @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name">
                            @error('first_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name Input -->
                        <div class="mb-6">
                            <label for="middle_name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Middle Name') }}</label>
                            <input id="middle_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('middle_name') border-red-500 @enderror" name="middle_name" value="{{ old('middle_name') }}" autocomplete="middle_name">
                            @error('middle_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name Input -->
                        <div class="mb-6">
                            <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Last Name') }}</label>
                            <input id="last_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('last_name') border-red-500 @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name">
                            @error('last_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Number Input -->
                        <div class="mb-6">
                            <label for="contact_number" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Contact Number') }}</label>
                            <input id="contact_number" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('contact_number') border-red-500 @enderror" name="contact_number" value="{{ old('contact_number') }}" required autocomplete="contact_number">
                            @error('contact_number')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- License Number Input -->
                        <div class="mb-6">
                            <label for="license_number" class="block text-gray-700 text-sm font-bold mb-2">{{ __('License Number') }}</label>
                            <input id="license_number" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('license_number') border-red-500 @enderror" name="license_number" value="{{ old('license_number') }}" required autocomplete="license_number">
                            @error('license_number')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-center">
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
