<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user1_id = $_SESSION['user_id'];
$user2_id = $_GET['id'] ?? null;

// Prevent sending requests to self
if (!$user2_id || $user1_id == $user2_id) {
    echo "❌ Invalid request.";
    exit;
}

// Check if a connection or request already exists
$check_sql = "SELECT * FROM connection_requests 
              WHERE (sender_id = ? AND receiver_id = ?) 
              OR (sender_id = ? AND receiver_id = ?)";

$stmt = $conn->prepare($check_sql);
$stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "⚠️ A request already exists between you and this user. <a href='profile.php'>Back</a>";
    exit;
}
$stmt->close();

// Insert new connection request
$insert_sql = "INSERT INTO connection_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("ii", $user1_id, $user2_id);

if ($stmt->execute()) {
    echo "✅ Connection request sent successfully! <a href='profile.php'>Back to Profile</a>";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
?>
