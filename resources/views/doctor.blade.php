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
            padding: 25px 0;
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
            padding: 15px 20px;
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
        .book-appointment-button {
            background-color: #0047AB; 
            color: white;
            padding: 20px 40px; 
            border: none;
            border-radius: 12px; 
            cursor: pointer;
            font-size: 24px; 
            transition: background-color 0.3s ease-in-out; 
        }
        .book-appointment-button:hover {
            background-color: #000080;
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
                            <a class="nav-link" href="{{route('doctor')}}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('appointment.index') }}">Appointments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.records') }}">Records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.generateReport') }}">Reports</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.calendar') }}">Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('restore.patient') }}">Archives</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.settings') }}">Settings</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Top Navbar -->
            <div class="col-md-10 ml-sm-auto col-lg-10 px-4 top-navbar">
                <h1 class="h2">Doctor Dashboard</h1>
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
                <div class="col-lg-4">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 small">Appointments Today</div>
                                        <div class="text-lg font-weight-bold">
                                            <?php
                                            $appointmentsTodayCount = App\Models\Appointment::whereHas('slot.bookingLimit', function ($query) {
                                                                            $query->whereDate('date', now()->toDateString());
                                                                        })->count();
                                            echo $appointmentsTodayCount;
                                            ?>
                                        </div>
                                    </div>
                                    <i class="fa fa-file-text fa-3x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="{{ route('appointment.index') }}">View Appointments</a>
                                <div class="small text-white">
                                    <i class="fas fa-angle-right"></i>
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
</body>
</html>
