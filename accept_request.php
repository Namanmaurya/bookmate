<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$receiver_id = $_SESSION['user_id'];
$sender_id = $_GET['id'] ?? null;

if (!$sender_id) {
    header('Location: profile.php');
    exit;
}

// Step 1: Update connection_requests to accepted
$stmt = $conn->prepare("UPDATE connection_requests SET status = 'accepted' WHERE sender_id = ? AND receiver_id = ?");
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();
$stmt->close();

// Optional Step 2: Add to a connections table (if it exists)
$check_sql = "SELECT * FROM connections WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $insert_stmt = $conn->prepare("INSERT INTO connections (user1_id, user2_id) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $sender_id, $receiver_id);
    $insert_stmt->execute();
    $insert_stmt->close();
}

$check_stmt->close();

// ✅ Redirect back to profile
header("Location: profile.php");
exit;
?>
