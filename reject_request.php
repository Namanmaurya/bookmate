<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$receiver_id = $_SESSION['user_id'];
$sender_id = $_GET['id'];

// Update the correct table — connection_requests
$sql = "UPDATE connection_requests 
        SET status = 'rejected' 
        WHERE sender_id = '$sender_id' AND receiver_id = '$receiver_id'";

if (mysqli_query($conn, $sql)) {
    // Optional: you can echo for debugging, or just redirect
    // echo "Request rejected.";
} else {
    echo "Error: " . mysqli_error($conn);
}

header("Location: profile.php");
exit;
?>
