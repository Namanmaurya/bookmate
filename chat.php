<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

$sql = "SELECT * FROM messages WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$user_id') ORDER BY timestamp ASC";
$result = mysqli_query($conn, $sql);

echo "<h3>Chat with User ID: $receiver_id</h3>";
while ($message = mysqli_fetch_assoc($result)) {
    $sender_sql = "SELECT username FROM users WHERE id = '" . $message['sender_id'] . "'";
    $sender_result = mysqli_query($conn, $sender_sql);
    $sender = mysqli_fetch_assoc($sender_result);
    
    echo "<strong>" . $sender['username'] . ":</strong> " . $message['message'] . "<br><br>";
}
?>
