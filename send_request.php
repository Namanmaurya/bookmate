<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['id']; // Get the user id of the person to connect with

// Check if a request already exists
$check_sql = "SELECT * FROM connection_requests 
              WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') 
              OR (sender_id = '$receiver_id' AND receiver_id = '$user_id')";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    // Insert the connection request as pending
    $insert_sql = "INSERT INTO connection_requests (sender_id, receiver_id, status) 
                   VALUES ('$user_id', '$receiver_id', 'pending')";
    mysqli_query($conn, $insert_sql);
}

header('Location: profile.php');
exit;
?>
