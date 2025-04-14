<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            font-family: 'Arial', sans-serif;
        }

        .hero-section {
            background-color: #fffae6;
            padding: 50px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #5a6268;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #4e555b;
        }

        .container {
            max-width: 800px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 20px;
        }

        .card-header {
            background-color: #fffae6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5 text-center">
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <h1 class="display-4 text-primary">Welcome to BookMate!</h1>
            <p class="lead text-muted">Find your perfect match based on books, genres, and more.</p>
        </div>

        <!-- Conditional Buttons -->
        <div class="card mt-5">
            <div class="card-header text-center">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <h3>Start Your Journey with BookMate</h3>
                <?php else: ?>
                    <h3>Welcome Back to BookMate</h3>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn btn-primary btn-custom me-3">Login</a>
                    <a href="register.php" class="btn btn-secondary btn-custom">Register</a>
                <?php else: ?>
                    <a href="profile.php" class="btn btn-primary btn-custom me-3">Go to Profile</a>
                    <a href="logout.php" class="btn btn-danger btn-custom">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Optional Bootstrap JS and Popper.js (if you plan to use Bootstrap JS components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
