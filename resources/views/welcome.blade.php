<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CarePoint Clinic Web-Based Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .custom-navbar {
            background-color: #007AFF;
            padding: 10px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 20px;
            position: relative;
            z-index: 2; 
        }

        .custom-navbar img {
            max-height: 100px;
        }

        .custom-navbar h5 {
            margin: 0;
            font-size: 1.5rem;
        }

        .custom-navbar ul {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .custom-navbar a {
            color: #fff;
            margin-right: 20px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .custom-navbar a:hover {
            color: #ffd700;
        }

        .background {
            background-image: url('20230827_081006.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            text-align: center;
            height: 100vh; 
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1; 
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .quote-text {
            font-family: 'Times New Roman', monospace;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .container.text-center.mt-4 {}

        .btn {
            font-size: 1rem;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: #007AFF;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body class="antialiased">
    <nav class="custom-navbar">
        <div>
            <img src="Carepoint Clinic Appointment Booking System Logo.png" alt="Carepoint Clinic">
            <h5>Carepoint Clinic Web-Based Appointment System</h5>
        </div>
        <ul>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/contact') }}">Contact Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/about') }}">About Us</a>
            </li>
        </ul>
    </nav>

    <div class="background">
        <div class="overlay">
            <div class="quote-text">
                <h1>"YOUR HEALTH, OUR PRIORITY"</h1>
                <h1>"EASY APPOINTMENTS AT YOUR FINGERTIPS!"</h1>
            </div>

            <div class="container text-center mt-4">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}">
                        <button type="button" class="btn btn-primary btn-lg">Login</button>
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">
                        <button type="button" class="btn btn-secondary btn-lg">Sign Up</button>
                    </a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
