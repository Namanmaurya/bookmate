<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookMate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background-color: #fff;
      color: #111;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background-color: #f8f9fa;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .navbar-brand {
      font-weight: bold;
      color: #111 !important;
    }

    .hero-section {
      text-align: center;
      padding: 60px 30px;
      margin-top: 40px;
      background-color: #f2f2f2;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
    }

    .btn-custom {
      padding: 12px 30px;
      font-size: 16px;
      border-radius: 30px;
      font-weight: bold;
      text-transform: uppercase;
      transition: 0.3s ease;
    }

    .btn-primary {
      background-color: #000;
      color: #fff;
      border: none;
    }

    .btn-primary:hover {
      background-color: #333;
      color: #fff;
    }

    .btn-secondary {
      background-color: #6c757d;
      color: #fff;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .btn-danger {
      background-color: #dc3545;
      color: #fff;
      border: none;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .card {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
      margin-top: 30px;
    }

    .card-header {
        background-color: #f2f2f2;
      border-bottom: 1px solid #ddd;
      font-weight: bold;
    }

    footer {
      text-align: center;
      padding: 20px;
      color: #777;
      margin-top: auto;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">📚 BookMate</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container">
    <div class="hero-section">
      <h1 class="display-4 fw-bold">Welcome to BookMate!</h1>
      <p class="lead">Find your perfect match through shared book interests and genres.</p>
    </div>

    <div class="card text-center mt-5">
      <div class="card-header">
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

  <!-- Footer -->
  <footer>
    &copy; <?= date("Y") ?> BookMate. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
