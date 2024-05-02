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
            font-size: 14px;
            font-family: 'Open Sans', sans-serif;
            margin-top: 25px;
            text-align: justify; 
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
            background-position: right;
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
            <div>
                <p>Welcome to Carepoint Medical Clinic, where your health and well-being are our top priorities. Our dedicated team of healthcare professionals is committed to providing you with personalized, high-quality medical care.</p>

                <p>At Carepoint Medical Clinic, we offer a wide range of services to meet your healthcare needs. From routine check-ups and general consultations to specialized treatments and vaccinations, we are here to support you at every step of your healthcare journey.</p>

                <p>We believe in the power of preventive care and early intervention to promote long-term health and wellness. That's why we emphasize the importance of regular check-ups and screenings to detect and address any health concerns before they become serious.</p>

                <p>Our clinic is equipped with state-of-the-art facilities and staffed by experienced healthcare professionals who are dedicated to providing compassionate and comprehensive care to every patient. Whether you're visiting us for a routine check-up or seeking treatment for a specific medical condition, you can trust that you'll receive the highest standard of care at Carepoint Medical Clinic.</p>

                <p>Thank you for choosing Carepoint Medical Clinic for your healthcare needs. We look forward to partnering with you in your journey towards optimal health and well-being.</p>
            </div>
        </div>

        <div class="background-image">
            <div class="overlay">
            </div>
        </div>
    </div>
</body>
</html>
