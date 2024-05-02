<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CarePoint Clinic Web-Based Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; 
        }

        .custom-navbar {
            background-color: #007AFF;
            padding: 10px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
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

        .content {
            display: flex;
            flex: 1; 
            overflow: hidden; 
            margin-top: 0px; 
        }

        .text-content {
            flex: 1; 
            padding: 20px;
            font-size: 1.2rem;
            margin-top: 100px; 
        }

        .text-content p {
            margin-bottom: 10px; 
        }

        .text-content i {
            margin-right: 10px; 
        }

        .background-image {
            background-image: url('20230827_081006.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            flex: 1;
        }

        .overlay {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            color: #fff;
        }

        .overlay img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
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
                <a class="nav-link active" aria-current="page" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/contact">Contact Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about">About Us</a>
            </li>
        </ul>
    </nav>
    <div class="content">
        <div class="text-content">
            <p><i class="fas fa-phone"></i> <span style="font-weight: bold;">Phone:</span> 09178886547</p>
            <p><i class="fas fa-map-marker-alt"></i> <span style="font-weight: bold;">Address:</span> Purok Mangga, Poblacion 1, Mabini, Bohol</p>
            <p><i class="fas fa-envelope"></i> <span style="font-weight: bold;">Email:</span> vanessa24853@gmail.com</p>
        </div>
        <div class="background-image">
            <div class="overlay">
            </div>
        </div>
    </div>
</body>
</html>
