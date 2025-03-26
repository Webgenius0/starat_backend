<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .error-container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .error-container h1 {
            font-size: 2.5rem;
            color: #ff4d4f;
            margin-bottom: 20px;
        }

        .error-container p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .error-container a {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .error-container a:hover {
            background-color: #0056b3;
        }

        .error-container a:focus {
            outline: none;
            box-shadow: 0 0 5px #007bff;
        }
    </style>
</head>
<body>
<div class="error-container">
    <h1>Access Denied</h1>
    <p>The registration page is not permitted yet.</p>
    <a href="{{ url('/') }}">Go Back</a>
</div>
</body>
</html>
