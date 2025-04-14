<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle profile image upload
    $image_name = $_FILES['profile_image']['name'];
    $image_tmp = $_FILES['profile_image']['tmp_name'];
    $image_path = 'uploads/' . time() . '_' . basename($image_name);

    // Ensure uploads directory exists
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    if (move_uploaded_file($image_tmp, $image_path)) {
        // Insert user data into database
        $sql = "INSERT INTO users (username, email, password, profile_image) 
                VALUES ('$username', '$email', '$password', '$image_path')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Registration successful! <a href='login.php'>Login now</a>";
        } else {
            $error_message = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Image upload failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BookMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Arial', sans-serif;
        }

        .register-container {
            max-width: 450px;
            margin: auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .register-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 25px;
            text-transform: uppercase;
            font-weight: bold;
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .register-btn:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .form-control {
            border-radius: 25px;
            padding: 12px;
        }

        .success-message,
        .error-message {
            text-align: center;
            margin-top: 10px;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="register-container">
            <div class="register-header">
                <h3>Register on BookMate</h3>
                <p class="text-muted">Create an account to connect with fellow book lovers</p>
            </div>

            <!-- Display success or error message -->
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <strong><?php echo $success_message; ?></strong>
                </div>
            <?php elseif (isset($error_message)): ?>
                <div class="error-message">
                    <strong><?php echo $error_message; ?></strong>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn register-btn">Register</button>
            </form>

            <!-- Redirect to Login page -->
            <div class="text-center mt-3">
                <p class="text-muted">Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <!-- Optional Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
