<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$request_id = $_GET['id'];
$sql = "UPDATE connections SET status = 'rejected' WHERE id = '$request_id'";
if (mysqli_query($conn, $sql)) {
    echo "Request rejected.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
