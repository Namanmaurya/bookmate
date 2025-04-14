<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];

// Ensure the receiver_id is passed in the URL
if (isset($_GET['receiver_id'])) {
    $receiver_id = intval($_GET['receiver_id']);
} else {
    echo "Receiver ID is missing.";
    exit;
}

// Check if the receiver exists
$check_receiver = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
$check_receiver->bind_param("i", $receiver_id);
$check_receiver->execute();
$check_receiver->store_result();

if ($check_receiver->num_rows === 0) {
    echo "The user you're trying to message does not exist.";
    exit;
}

$check_receiver->bind_result($receiver_id, $receiver_username);
$check_receiver->fetch();
$check_receiver->close();

// Check if sender and receiver are connected (must have an accepted connection request)
$check_connection = $conn->prepare("
    SELECT id FROM connection_requests 
    WHERE (
        (sender_id = ? AND receiver_id = ?) OR 
        (sender_id = ? AND receiver_id = ?)
    ) AND status = 'accepted'
");
$check_connection->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$check_connection->execute();
$check_connection->store_result();

if ($check_connection->num_rows === 0) {
    echo "You are not connected with this user.";
    exit;
}
$check_connection->close();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message']) && !empty(trim($_POST['message']))) {
        $message = trim($_POST['message']);

        // Insert message using prepared statement
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

        if ($stmt->execute()) {
            header("Location: message.php?receiver_id=$receiver_id&sent=1");
            exit;
        } else {
            echo "Error sending message: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h3>Send Message to <?php echo htmlspecialchars($receiver_username); ?></h3>

        <!-- Message Form -->
        <form method="POST">
            <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($receiver_id); ?>">
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea name="message" id="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>

            <a href="profile.php " class="btn btn-primary">Back</a>
        </form>

        <?php if (isset($_GET['sent'])): ?>
            <div class="alert alert-success mt-3">Message sent successfully!</div>
        <?php endif; ?>

    </div>

</body>
</html>
