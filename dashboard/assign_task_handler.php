<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $providerId = (int)$_POST['provider_id'];
    $issueId = (int)$_POST['issue_id'];
    $adminId = (int)$_POST['admin_id'];

    // Get citizen ID from issue
    $result = $conn->query("SELECT citizen_id FROM issues WHERE id = $issueId");
    $citizen = $result->fetch_assoc();
    $citizenId = $citizen['citizen_id'];

    $stmt = $conn->prepare("INSERT INTO service_requests (citizen_id, provider_id, issue_id, status, payment_status, admin_id) VALUES (?, ?, ?, 'in_progress', 'pending', ?)");
    $stmt->bind_param("iiii", $citizenId, $providerId, $issueId, $adminId);
    $stmt->execute();

    $conn->query("UPDATE issues SET status = 'in_progress', hired_service_provider_id = $providerId WHERE id = $issueId");

    echo "Success";
}
?>
