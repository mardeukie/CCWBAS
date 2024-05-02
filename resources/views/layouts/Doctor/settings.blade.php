@extends('layouts.Doctor.navbar')

@section('content')
<style>
        .container {
            margin-top: 50px;
        }
        #updateButton {
            margin-top: 20px; 
            padding: 10px; 
            width: 100%; 
        }
        #bootstrapContent {
            margin: 0 auto; 
            width: 90%; 
            max-width: 400px; 
            border: 2px solid #333; 
            border-radius: 10px; 
            padding: 20px; 
        }
        #updateForm input[type="password"] {
            width: 100%;
            background-color: #f8f9fa; 
            border: 1px solid #ced4da; 
            border-radius: 5px; 
            padding: 12px; 
            margin-bottom: 20px; 
        }
        #bootstrapContent label {
            text-align: left; 
            display: block; 
            font-weight: bold;
            margin-bottom: 8px; 
            color: #333; 
        }
    </style>
    
    <div class="container"> 
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="bootstrapContent" class="card">
                    <div class="card-header text-center" style="font-weight: bold; border-radius: 10px 10px 0 0;">
                        {{ __('Change Password') }}
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="updateForm" method="POST" action="{{ route('patient.settings') }}">
                            @csrf

                            <div class="form-group">
                                <label for="current_password">{{ __('Current Password') }}</label>
                                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password">{{ __('New Password') }}</label>
                                <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required autocomplete="new-password">
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">{{ __('Confirm Password') }}</label>
                                <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" required autocomplete="new-password">
                                @error('confirm_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" id="updateButton" class="btn btn-primary">
                                    {{ __('Update Password') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


