<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$senderId = isset($_POST['sender_id']) ? (int)$_POST['sender_id'] : 0;
$receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$issueId = isset($_POST['issue_id']) ? (int)$_POST['issue_id'] : 0;
$message = trim($_POST['message'] ?? '');

if (!$senderId || !$receiverId || !$issueId || empty($message)) {
    http_response_code(400);
    echo "Missing required fields.";
    exit;
}

// Insert the message into the database
$stmt = $conn->prepare("
    INSERT INTO messages (sender_id, receiver_id, issue_id, message, is_read)
    VALUES (?, ?, ?, ?, 0)
");

$stmt->bind_param("iiis", $senderId, $receiverId, $issueId, $message);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Message sent successfully.";
} else {
    http_response_code(500);
    echo "Failed to send message.";
}

$stmt->close();
$conn->close();
?>
