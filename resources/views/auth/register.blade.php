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
    <script>
        function validateForm() {
            var name = document.getElementById('name').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password-confirm').value;
            
            // Basic validation: Ensure required fields are not empty
            if (name === '') {
                alert('Please enter your name.');
                return false;
            }

            if (email === '') {
                alert('Please enter your email address.');
                return false;
            }

            if (password === '') {
                alert('Please enter a password.');
                return false;
            }

            if (confirmPassword === '') {
                alert('Please confirm your password.');
                return false;
            }

            // Validate email format
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }

            // Validate password length
            if (password.length < 8) {
                alert('Password must be at least 8 characters long.');
                return false;
            }

            // Validate password match
            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }

            // If everything is valid, return true to submit the form
            return true;
        }
    </script>
</head>
<body style="background-color: #007AFF;">
<section>
    <div class="relative h-screen">
        <div class="absolute inset-0 bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-opacity-50 backdrop-filter backdrop-blur-sm"></div>
        <div class="relative z-10 flex justify-center items-center h-full">
            <div class="w-full max-w-md">
                <div class="bg-white shadow-md rounded-lg px-8 py-6">
                    <h2 class="text-center text-2xl font-bold mb-6">{{ __('Sign Up') }}</h2>
                    <form method="POST" action="{{ route('register') }}" onsubmit="return validateForm()">
                        {{ csrf_field() }}
                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Name') }}</label>
                            <input id="name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Password') }}</label>
                            <div class="relative">
                                <input id="password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password">
                            </div>
                            @error('password')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Confirm Password') }}</label>
                            <div class="relative">
                                <input id="password-confirm" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="role" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Role') }}</label>
                            <div class="relative">
                                <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="1">Patient</option>
                                    <option value="2">Medical Staff</option>
                                    <option value="3">Doctor</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-center">
                            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Sign Up') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>
