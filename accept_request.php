<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sender_id = $_GET['id'];

// Update the status to accepted
$update_sql = "UPDATE connection_requests 
               SET status = 'accepted' 
               WHERE sender_id = '$sender_id' AND receiver_id = '$user_id'";
mysqli_query($conn, $update_sql);

header('Location: profile.php');
exit;
?>
