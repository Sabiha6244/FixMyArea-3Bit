<?php
// fetch_messages.php

header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed."]));
}

$senderId = isset($_GET['sender_id']) ? (int)$_GET['sender_id'] : 0;
$receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$issueId = isset($_GET['issue_id']) ? (int)$_GET['issue_id'] : 0;

if (!$senderId || !$receiverId || !$issueId) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT * FROM messages 
    WHERE issue_id = ? 
      AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
    ORDER BY timestamp ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $issueId, $senderId, $receiverId, $receiverId, $senderId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
