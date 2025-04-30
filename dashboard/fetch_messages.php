<?php
// fetch_messages.php

header('Content-Type: application/json');

// Connect to the database
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed."]));
}

// Get parameters
$senderId = isset($_GET['sender_id']) ? (int)$_GET['sender_id'] : 0;
$receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$issueId = isset($_GET['issue_id']) ? (int)$_GET['issue_id'] : 0;

// Validate
if (!$senderId || !$receiverId || !$issueId) {
    echo json_encode([]);
    exit;
}

// Fetch messages along with sender info
$sql = "
    SELECT 
        m.*, 
        u.name AS sender_name, 
        COALESCE(NULLIF(u.profile_picture, ''), 'uploads/default-profile.png') AS profile_picture
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.issue_id = ? 
      AND ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)) 
    ORDER BY m.timestamp ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $issueId, $senderId, $receiverId, $receiverId, $senderId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // If only filename is stored, prepend the path
    if (!str_starts_with($row['profile_picture'], 'http') && !str_starts_with($row['profile_picture'], '../')) {
        $row['profile_picture'] = '../' . $row['profile_picture'];
    }
    $messages[] = $row;
}

echo json_encode($messages);
?>
