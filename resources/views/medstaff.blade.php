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
                <div class="container">
                    <!-- Month Label -->
                    <div class="form-group mt-3">
                        <label for="monthInput">Select Month:</label>
                        <input type="month" id="monthInput" class="form-control" onchange="getMonthData(this.value)">
                    </div>
                <!-- Chart Section -->
                <div class="row mt-3">
                    <!-- Doughnut Chart -->
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <canvas id="doughnutChart" width="400" height="400"></canvas>
                        </div>
                    </div>
                    <!-- Bar Chart -->
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <canvas id="barChart" width="400" height="400"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Dropdown and Line Chart Section -->
                <div class="row mt-3">
                    <!-- Dropdown -->
                    <div class="col-lg-12">
                        <div class="chart-container">
                            <canvas id="lineChart" width="300" height="200"></canvas>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script>
    var ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
    var doughnutChart = new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {!! json_encode($appointmentsData) !!},
        options: {
            // Customize chart options if needed
        }
    });

    var ctxBar = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode($appointmentTypeLabels) !!},
            datasets: [{
                label: 'Appointment Count by Type',
                data: {!! json_encode($appointmentTypeData) !!},
                backgroundColor: '#36a2eb',
                borderColor: '#36a2eb',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

function getMonthData(selectedMonth) {
    $.ajax({
        url: '/medstaff',
        method: 'GET',
        data: { selected_month: selectedMonth },
        success: function(response) {
            // Update the Doughnut Chart
            doughnutChart.data = response.appointmentsData;
            doughnutChart.update();
            
            // Update the Bar Chart
            barChart.data.labels = response.appointmentTypeLabels;
            barChart.data.datasets[0].data = response.appointmentTypeData;
            barChart.update();
            
            // Update the Line Chart
            updateLineChart(response.volumeData.labels, response.volumeData.data);
        },
        error: function(xhr, status, error) {
            // Handle errors if any
        }
    });
}

var lineChart;

function createLineChart(labels, data) {
    var ctxLine = document.getElementById('lineChart').getContext('2d');
    lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Appointment Volume',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                pointRadius: 5,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Appointment Volume'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
}

function updateLineChart(labels, data) {
    // Update the line chart with new data
    lineChart.data.labels = labels;
    lineChart.data.datasets[0].data = data;
    lineChart.update();
}

// Initialize the line chart
createLineChart({!! json_encode($volumeData['labels']) !!}, {!! json_encode($volumeData['data']) !!});

</script>

</body>
</html>
