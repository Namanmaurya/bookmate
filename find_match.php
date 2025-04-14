<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Get user's preferences
$age = $user['age'];
$books = $user['books'];
$genres = $user['genres'];

// Find potential matches based on profile preferences
$sql_matches = "SELECT * FROM users WHERE id != '$user_id' AND age BETWEEN " . ($age - 5) . " AND " . ($age + 5);
$matches_result = mysqli_query($conn, $sql_matches);

echo "<h3>Potential Matches:</h3>";
while ($match = mysqli_fetch_assoc($matches_result)) {
    echo "Match: " . $match['username'] . "<br>";
    echo "Books: " . $match['books'] . "<br>";
    echo "Genres: " . $match['genres'] . "<br>";
    echo "<a href='send_request.php?match_id=" . $match['id'] . "'>Send Connection Request</a><br><br>";
}
?>
