<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Laravel</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .navbar-custom {
            background-color: rgba(0, 0, 0, 0.7);
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.3);
        }
        .btn-custom {
            border-radius: 30px;
            background: #fff;
            color: #ff758c;
            padding: 10px 20px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: #ff758c;
            color: #fff;
        }
        .hero-text {
            text-align: center;
            margin-top: 50px;
        }
        .hero-text h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero-text p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand text-white" href="{{ url('/') }}">
            <strong>Laravel</strong>
        </a>
        <div class="d-flex">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-custom me-2">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-custom me-2">
                        Log In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-custom">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<div class="hero-text">
    <h1>Welcome to Laravel</h1>
    <p>Your journey with Laravel starts here! Build modern, fast, and scalable applications effortlessly.</p>
    <div>
        <a href="{{ route('login') }}" class="btn btn-custom me-2">Get Started</a>
        <a href="https://laravel.com/docs" class="btn btn-custom">Learn More</a>
    </div>
</div>

<!-- Add Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
