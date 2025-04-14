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

// Escape genres and books early to avoid undefined warnings
$genres = mysqli_real_escape_string($conn, $user['genres']);
$books = mysqli_real_escape_string($conn, $user['books']);

// Fetch incoming requests
$requests_sql = "SELECT * FROM connection_requests 
                 JOIN users ON connection_requests.sender_id = users.id 
                 WHERE connection_requests.receiver_id = '$user_id' AND connection_requests.status = 'pending'";
$requests_result = mysqli_query($conn, $requests_sql);

// Fetch connections
$connections_sql = "
    SELECT u.* FROM users u
    JOIN connection_requests cr ON (
        (cr.sender_id = u.id AND cr.receiver_id = '$user_id') OR
        (cr.receiver_id = u.id AND cr.sender_id = '$user_id')
    )
    WHERE cr.status = 'accepted' AND (cr.sender_id = '$user_id' OR cr.receiver_id = '$user_id')
";
$connections_result = mysqli_query($conn, $connections_sql);

// Get connected user IDs
$connections_ids_sql = "SELECT u.id FROM users u
    JOIN connection_requests cr ON (
        (cr.sender_id = u.id AND cr.receiver_id = '$user_id') OR
        (cr.receiver_id = u.id AND cr.sender_id = '$user_id')
    )
    WHERE cr.status = 'accepted' AND (cr.sender_id = '$user_id' OR cr.receiver_id = '$user_id')";
$connections_ids_result = mysqli_query($conn, $connections_ids_sql);

$connected_users = [];
while ($row = mysqli_fetch_assoc($connections_ids_result)) {
    $connected_users[] = $row['id'];
}

// Suggested BookMates
$suggest_sql = "SELECT * FROM users 
    WHERE id != '$user_id' 
    AND (genres LIKE '%$genres%' OR books LIKE '%$books%')";

if (!empty($connected_users)) {
    $connected_users_ids = implode(',', $connected_users);
    $suggest_sql .= " AND id NOT IN ($connected_users_ids)";
}
$suggest_result = mysqli_query($conn, $suggest_sql);

// Fetch messages
$messages_sql = "
    SELECT messages.*, users.username 
    FROM messages 
    JOIN users ON messages.sender_id = users.id 
    WHERE messages.receiver_id = '$user_id' 
    ORDER BY messages.created_at DESC
";
$messages_result = mysqli_query($conn, $messages_sql);

// Notification counts
$incomingRequestCount = mysqli_num_rows($requests_result);
$suggestedMateCount = mysqli_num_rows($suggest_result);
$connectionsCount = mysqli_num_rows($connections_result);
$incomingMessagesCount = mysqli_num_rows($messages_result);

$profileUpdated = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $age = $_POST['age'];
        $books = $_POST['books'];
        $genres = $_POST['genres'];

        $stmt = $conn->prepare("UPDATE users SET age = ?, books = ?, genres = ? WHERE id = ?");
        $stmt->bind_param("sssi", $age, $books, $genres, $user_id);

        if ($stmt->execute()) {
            $profileUpdated = true;
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user = mysqli_fetch_assoc($result);
        }

        $stmt->close();
    }

    if (isset($_POST['add_book'])) {
        $title = $_POST['title'];
        $genre = $_POST['genre'];

        $stmt = $conn->prepare("INSERT INTO books (user_id, title, genre) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $genre);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['send_reply'])) {
        $receiver_id = $_POST['receiver_id'];
        $reply_message = mysqli_real_escape_string($conn, $_POST['reply_message']);

        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $receiver_id, $reply_message);
        $stmt->execute();
        $stmt->close();

        // Refresh messages list
        $messages_result = mysqli_query($conn, "
            SELECT messages.*, users.username 
            FROM messages 
            JOIN users ON messages.sender_id = users.id 
            WHERE messages.receiver_id = '$user_id' 
            ORDER BY messages.created_at DESC
        ");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - BookMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
        }

        .card-style {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #777;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-light bg-light justify-content-between px-4">
        <a class="navbar-brand" href="profile.php">📖 BookMate</a>

        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary" onclick="toggleSection('incomingRequests')">
                Incoming Requests (<?php echo $incomingRequestCount; ?>)
            </button>
            <button class="btn btn-outline-success" onclick="toggleSection('suggestedMates')">
                Suggested BookMates (<?php echo $suggestedMateCount; ?>)
            </button>
            <button class="btn btn-outline-info" onclick="toggleSection('connections')">
                Your Connections (<?php echo $connectionsCount; ?>)
            </button>
            <button class="btn btn-outline-warning" onclick="toggleSection('incomingMessages')">
                Incoming Messages (<?php echo $incomingMessagesCount; ?>)
            </button>
        </div>

        <a href="logout.php" class="btn btn-danger">Logout</a>
    </nav>

    <div class="container mt-5">
        <div class="row g-4">
            <!-- Profile Section -->
            <div class="col-md-6">
                <div class="card-style">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image"
                            class="img-fluid rounded-circle mb-3"
                            style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;">
                    <?php else: ?>
                        <img src="default-profile.png" alt="Default Profile" class="img-fluid rounded-circle mb-3"
                            style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ccc;">
                    <?php endif; ?>
                    <h3>Welcome, <?php echo htmlspecialchars($user['username']); ?></h3>
                    <?php if ($profileUpdated): ?>
                        <div class="alert alert-success">Profile updated successfully!</div>
                    <?php endif; ?>
                    <button class="btn btn-outline-primary w-100 mb-3" id="toggleFormBtn">Update Profile</button>
                    <div id="updateForm" style="display: none;">
                        <form method="POST">
                            <input type="number" name="age" class="form-control mb-2" placeholder="Age"
                                value="<?php echo htmlspecialchars($user['age']); ?>">
                            <input type="text" name="books" class="form-control mb-2" placeholder="Favorite Books"
                                value="<?php echo htmlspecialchars($user['books']); ?>">
                            <input type="text" name="genres" class="form-control mb-2" placeholder="Favorite Genres"
                                value="<?php echo htmlspecialchars($user['genres']); ?>">
                            <button type="submit" name="update_profile" class="btn btn-success w-100">Save</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Incoming Requests -->
            <div id="incomingRequests" class="col-md-6" style="display: none;">
                <div class="card-style">
                    <h4>Incoming Requests</h4>
                    <ul class="list-group">
                        <?php mysqli_data_seek($requests_result, 0);
                        while ($req = mysqli_fetch_assoc($requests_result)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($req['username']); ?>
                                <span>
                                    <a href="accept_request.php?id=<?php echo $req['sender_id']; ?>"
                                        class="btn btn-sm btn-success">Accept</a>
                                    <a href="reject_request.php?id=<?php echo $req['sender_id']; ?>"
                                        class="btn btn-sm btn-danger">Reject</a>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Suggested BookMates -->
            <div id="suggestedMates" class="col-md-6" style="display: none;">
                <div class="card-style">
                    <h4>Suggested BookMates</h4>
                    <ul class="list-group">
                        <?php mysqli_data_seek($suggest_result, 0);
                        while ($suggest = mysqli_fetch_assoc($suggest_result)): ?>
                            <?php
                            $receiver_id = $suggest['id'];
                            $check_query = "SELECT status FROM connection_requests 
                            WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') 
                            OR (sender_id = '$receiver_id' AND receiver_id = '$user_id')";
                            $check_result = mysqli_query($conn, $check_query);
                            $request_status = mysqli_fetch_assoc($check_result);
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($suggest['username']); ?>
                                (<?php echo htmlspecialchars($suggest['genres']); ?>)
                                <?php if ($request_status): ?>
                                    <?php if ($request_status['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Pending</button>
                                    <?php else: ?>
                                        <a href="send_request.php?id=<?php echo $receiver_id; ?>"
                                            class="btn btn-sm btn-primary">Connect</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="send_request.php?id=<?php echo $receiver_id; ?>"
                                        class="btn btn-sm btn-primary">Connect</a>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Connections -->
            <div id="connections" class="col-md-6" style="display: none;">
                <div class="card-style">
                    <h4>Your Connections</h4>
                    <ul class="list-group">
                        <?php mysqli_data_seek($connections_result, 0);
                        while ($connUser = mysqli_fetch_assoc($connections_result)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($connUser['username']); ?>
                                <a href="message.php?receiver_id=<?php echo $connUser['id']; ?>"
                                    class="btn btn-sm btn-outline-secondary">Message</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Incoming Messages -->
            <div id="incomingMessages" class="col-md-6" style="display: none;">
                <div class="card-style">
                    <h4>Incoming Messages</h4>
                    <ul class="list-group">
                        <?php mysqli_data_seek($messages_result, 0);
                        while ($msg = mysqli_fetch_assoc($messages_result)): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong><br>
                                <?php echo htmlspecialchars($msg['message']); ?><br>
                                <button class="btn btn-sm btn-outline-primary mt-2"
                                    onclick="toggleReplyForm(<?php echo $msg['sender_id']; ?>)">Reply</button>
                                <form method="POST" class="mt-2 reply-form" id="replyForm_<?php echo $msg['sender_id']; ?>"
                                    style="display: none;">
                                    <input type="hidden" name="receiver_id" value="<?php echo $msg['sender_id']; ?>">
                                    <textarea name="reply_message" class="form-control mb-2" rows="2"
                                        placeholder="Write your reply..." required></textarea>
                                    <button type="submit" name="send_reply" class="btn btn-sm btn-success">Send
                                        Reply</button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Add Book Form -->
            <div class="col-md-6">
                <div class="card-style">
                    <h3>Add Book</h3>
                    <button class="btn btn-outline-primary w-100 mb-3" id="toggleBookFormBtn">Add New</button>
                    <div id="addBookForm" style="display: none;">
                        <form method="POST">
                            <input type="text" name="title" class="form-control mb-2" placeholder="Book Title" required>
                            <input type="text" name="genre" class="form-control mb-2" placeholder="Genre" required>
                            <button type="submit" name="add_book" class="btn btn-success w-100">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; <?= date("Y") ?> BookMate. All rights reserved.
    </footer>

    <script>
        document.getElementById('toggleFormBtn').addEventListener('click', function () {
            const form = document.getElementById('updateForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        document.getElementById('toggleBookFormBtn').addEventListener('click', function () {
            const form = document.getElementById('addBookForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        function toggleSection(sectionId) {
            const sections = ['incomingRequests', 'suggestedMates', 'connections', 'incomingMessages'];
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (id === sectionId) {
                    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
                } else {
                    el.style.display = 'none';
                }
            });
        }

        function toggleReplyForm(id) {
            const form = document.getElementById(`replyForm_${id}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>