<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carepoint Clinic Web-Based Appointment System</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Vite Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8f9fa; 
        }

        #app {
            display: flex;
        }

        /* Sidebar styles */
        .universal-sidebar {
            background-color: #007AFF;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 50px 0;
            overflow-x: hidden;
        }

        .user-profile {
            text-align: center;
            padding: 20px;
        }

        .user-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .universal-sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }

        .universal-sidebar a:hover {
            background-color: #555; 
        }

        /* Top Navbar styles */
        .top-navbar {
            background-color: #007AFF; 
            color: white; 
            padding: 15px;
            margin-left: 250px; 
            display: flex;
            justify-content: space-between;
        }

        /* Logout Button styles */
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .logout-button form {
            margin: 0; 
        }

        .logout-button button {
            background-color: #007AFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button button:hover {
            background-color: #0056b3; 
        }

        .card-container {
            display: flex;
            justify-content: flex-start; 
            align-items: center;
            height: auto;
            margin-top: 20px;
        }

        .card {
            width: 100%; 
            max-width: 400px; 
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08); 
        }

        .card-content {
            padding: 20px;
        }

        .card-header {
            font-size: 1.25rem; 
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1.5rem; 
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 1rem; 
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem; 
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .appointment-label {
            font-weight: bold;
            color: #333;
            margin-right: 5px;
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Universal Sidebar -->
            <nav class="col-md-2 d-none d-md-block universal-sidebar">
                <div class="sidebar-sticky">
                    <!-- User Profile -->
                    <div class="user-profile">
                        <img src="user.jpg" alt="User Profile Image" class="img-fluid">
                        <p style="color: white; font-weight: bold;">{{ auth()->user()->name }}</p>
                    </div>
                    <!-- Navigation Links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('patient')}}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patient.appointments') }}">Appointments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patient.slots') }}">Slots</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patient.show') }}">Records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patient.calendar') }}">Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('settings.patient') }}">Settings</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Top Navbar -->
            <div class="col-md-10 ml-sm-auto col-lg-10 px-4 top-navbar">
                <h1 class="h2">Patient Dashboard</h1>
                <!-- Logout Button -->
                <div class="logout-button">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Main content -->
            <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h4>Welcome, {{ auth()->user()->name }}!</h4>
                    </div>
                </div>
                <div class="row mt-3 ml-3 mr-3">
                    <div class="col-lg-12">
                        <div class="card-container">
                            <div class="card">
                                <div class="card-content">
                                    <h5 class="card-header">Upcoming Appointments</h5>
                                    <div class="card-body">
                                        <?php
                                        $patient = Auth::user()->patient;

                                        $nextAppointment = $patient->appointments()
                                        ->whereHas('slot.bookingLimit', function ($query) {
                                            $query->where('date', '>=', now()->toDateString());
                                        })
                                        ->where('appointments.status', 'booked') // Specify table name for the status column
                                        ->join('slots', 'appointments.slot_id', '=', 'slots.id')
                                        ->join('booking_limits', 'slots.booking_limit_id', '=', 'booking_limits.id')
                                        ->orderBy('booking_limits.date')
                                        ->first();
                                        if ($nextAppointment) {
                                            echo "<p class='card-text'><span class='appointment-label'>Date:</span> " . date('F j, Y', strtotime($nextAppointment->slot->bookingLimit->date)) . "</p>";
                                            echo "<p class='card-text'><span class='appointment-label'>Location:</span> Purok Manga, Poblacion 1, Mabini, Bohol</p>";
                                        } else {
                                            echo "<p>No upcoming appointments.</p>";
                                        }
                                        ?>
                                        <a href="{{ route('patient.appointments') }}" class="btn btn-primary">Manage Appointment</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include Laravel Echo and Pusher Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('e2167373dbc755142fdb', {
        cluster: 'ap1'
        });

        var channel = pusher.subscribe('appointment-channel');
        channel.bind('appointment-cancelled', function(data) {
        alert(JSON.stringify(data));
        });
    </script>
</body>
</html>
