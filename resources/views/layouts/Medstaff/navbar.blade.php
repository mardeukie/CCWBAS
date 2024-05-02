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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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
            padding: 10px 0;
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
            padding: 10px 20px;
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
        <nav class="col-md-2 d-none d-md-block universal-sidebar">
                <div class="sidebar-sticky">
                    <!-- User Profile -->
                    <div class="user-profile">
                        <img src="{{ asset('user.jpg') }}" alt="User Profile Image" class="img-fluid">
                        <p style="color: white; font-weight: bold;">{{ auth()->user()->name }}</p>
                    </div>
                    <!-- Navigation Links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('medstaff')}}">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAppointments" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Appointments
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownAppointments" style="background-color: #333;">
                                <a class="dropdown-item" href="{{ route('appointments.today') }}" style="color: white;">Appointments Today</a>
                                <a class="dropdown-item" href="{{ route('appointments.tomorrow') }}" style="color: white;">Upcoming Appointments</a>
                                <a class="dropdown-item" href="{{ route('booked.appointments') }}" style="color: white;">All Appointments</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('medstaff.slots') }}">Slots</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('medical_records.index') }}">Records</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('generate.report') }}">Reports</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('medstaff.calendar') }}">Calendar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('restore') }}">Archives</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('settings') }}">Settings</a>
                        </li>
                    </ul>
                </div>
            </nav><

            <!-- Top Navbar -->
            <div class="col-md-10 ml-sm-auto col-lg-10 px-4 top-navbar">
                <h1 class="h2">Carepoint Medical Clinic</h1>
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
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
 


    <script>
        $(document).ready( function () {
            $('#myDataTable').DataTable({
                "ordering": false
            });
        });
    </script>
</body>
</html>
