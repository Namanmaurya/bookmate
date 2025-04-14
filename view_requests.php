<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM connections WHERE user2_id = '$user_id' AND status = 'pending'";
$requests = mysqli_query($conn, $sql);

echo "<h3>Pending Connection Requests:</h3>";
while ($request = mysqli_fetch_assoc($requests)) {
    $requesting_user_id = $request['user1_id'];
    $sql_user = "SELECT * FROM users WHERE id = '$requesting_user_id'";
    $result_user = mysqli_query($conn, $sql_user);
    $requesting_user = mysqli_fetch_assoc($result_user);

    echo "Connection request from: " . $requesting_user['username'] . "<br>";
    echo "<a href='accept_request.php?id=" . $request['id'] . "'>Accept</a> | <a href='reject_request.php?id=" . $request['id'] . "'>Reject</a><br><br>";
}
?>
