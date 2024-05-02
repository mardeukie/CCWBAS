//CHARTS

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carepoint Clinic Web-Based Appointment System</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Styles -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

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
                            <a class="nav-link" href="{{ route('appointment.report') }}">Reports</a>
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
            </nav>

            <!-- Top Navbar -->
            <div class="col-md-10 ml-sm-auto col-lg-10 px-4 top-navbar">
                <h1 class="h2">Medical Staff Dashboard</h1>
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
                        <div class="card bg-warning text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 small">Appointments Today</div>
                                        <div class="text-lg font-weight-bold">
                                            <?php
                                                $appointmentsTodayCount = App\Models\Appointment::whereHas('slot.bookingLimit', function ($query) {
                                                    $query->whereDate('date', now()->toDateString());
                                                })->where('status', 'booked')->count();
                                                echo $appointmentsTodayCount;
                                            ?>
                                        </div>
                                    </div>
                                    <i class="fa fa-file-text fa-3x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="{{ route('appointments.today') }}">View Appointments</a>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-4">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 small">Slots Available</div>
                                        <div class="text-lg font-weight-bold">
                                            <?php
                                            $slotsAvailableCount = App\Models\Slot::where('status', 'available')->count();
                                            echo $slotsAvailableCount;
                                            ?>
                                        </div>
                                    </div>
                                    <i class="fa fa-calendar fa-3x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="{{ route('medstaff.slots') }}">View Slots</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 small">Total Patients</div>
                                        <div class="text-lg font-weight-bold">
                                            <?php
                                            $totalPatientsCount = App\Models\Patient::count();
                                            echo $totalPatientsCount;
                                            ?>
                                        </div>
                                    </div>
                                    <i class="fa fa-user-md fa-3x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="{{ route('medical_records.index') }}">View Records</a>
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






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart.js Example</title>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-btn {
            background-color: #007AFF;
            color: #FFF;
            padding: 10px 20px;
            border: none;
            border-radius: 10px; /* Changed border-radius to 10px */
            cursor: pointer;
            margin-right: 10px;
            margin-bottom: 20px; /* Added margin-bottom for space */
            outline: none;
            transition: background-color 0.3s;
        }

        .chart-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: right; margin-bottom: 20px; margin-right:90px;"> <!-- Added margin-bottom -->
        <div style="width: 40%; text-align: center;">
            <div style="margin-left: 120px; margin-top: 20px;"> <!-- Added margin-top -->
                <button id="dailyPieBtn" class="chart-btn">Daily</button>
                <button id="weeklyPieBtn" class="chart-btn">Weekly</button>
                <button id="monthlyPieBtn" class="chart-btn">Monthly</button>
            </div>
            <canvas id="pieChart" width="300" height="300" style="margin-left: 100px;"></canvas>
        </div>
    
        <div style="width: 40%; text-align: center;">
            <div style="margin-top: 20px;"> <!-- Added margin-top -->
                <button id="dailyBarBtn" class="chart-btn">Daily</button>
                <button id="weeklyBarBtn" class="chart-btn">Weekly</button>
                <button id="monthlyBarBtn" class="chart-btn">Monthly</button>
            </div>
            <canvas id="barChart" width="300" height="300"></canvas>
        </div>
    </div>
    
    <div style="display: flex; justify-content: right; margin-bottom: 20px; margin-left: 185px;"> <!-- Added margin-bottom -->
        <div style="width: 100%;">
            <div style="width: 100%; text-align: center;">
                <button id="dailyLineBtn" class="chart-btn">Daily</button>
                <button id="weeklyLineBtn" class="chart-btn">Weekly</button>
                <button id="monthlyLineBtn" class="chart-btn">Monthly</button>
            </div>
            <div style="text-align: center; margin-top: 20px;"> <!-- Added margin-top -->
                <canvas id="lineChart" width="800" height="300" style="margin: 0 auto;"></canvas>
            </div>
        </div>
    </div>
    

    <script>
        // Sample data
        const data = {
            daily: [30, 40, 50, 60, 70, 80, 90],
            weekly: [100, 200, 300, 400, 500],
            monthly: [600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700]
        };

        const labels = {
            daily: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            weekly: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
            monthly: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        };

        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const ctxBar = document.getElementById('barChart').getContext('2d');
        const ctxLine = document.getElementById('lineChart').getContext('2d');

        let pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: labels.daily,
                datasets: [{
                    data: data.daily,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8A2BE2', '#00FF00', '#FF8C00', '#FFD700']
                }]
            }
        });

        let barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels.weekly,
                datasets: [{
                    label: 'Weekly Data',
                    data: data.weekly,
                    backgroundColor: '#007AFF'
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        let lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels.monthly,
                datasets: [{
                    label: 'Monthly Data',
                    data: data.monthly,
                    borderColor: 'orange',
                    borderWidth: 2,
                    fill: false
                }]
            }
        });

        // Button event listeners for Pie Chart
        document.getElementById('dailyPieBtn').addEventListener('click', () => {
            updateChart(pieChart, labels.daily, data.daily);
        });

        document.getElementById('weeklyPieBtn').addEventListener('click', () => {
            updateChart(pieChart, labels.weekly, data.weekly);
        });

        document.getElementById('monthlyPieBtn').addEventListener('click', () => {
            updateChart(pieChart, labels.monthly, data.monthly);
        });

        // Button event listeners for Bar Chart
        document.getElementById('dailyBarBtn').addEventListener('click', () => {
            updateChart(barChart, labels.daily, data.daily);
        });

        document.getElementById('weeklyBarBtn').addEventListener('click', () => {
            updateChart(barChart, labels.weekly, data.weekly);
        });

        document.getElementById('monthlyBarBtn').addEventListener('click', () => {
            updateChart(barChart, labels.monthly, data.monthly);
        });

        // Button event listeners for Line Chart
        document.getElementById('dailyLineBtn').addEventListener('click', () => {
            updateChart(lineChart, labels.daily, data.daily);
        });

        document.getElementById('weeklyLineBtn').addEventListener('click', () => {
            updateChart(lineChart, labels.weekly, data.weekly);
        });

        document.getElementById('monthlyLineBtn').addEventListener('click', () => {
            updateChart(lineChart, labels.monthly, data.monthly);
        });

        // Function to update chart
        function updateChart(chart, newLabels, newData) {
            chart.data.labels = newLabels;
            chart.data.datasets[0].data = newData;
            chart.update();
        }
    </script>
</body>
</html>