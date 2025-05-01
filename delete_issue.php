<?php
session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid issue ID.");
}

$issueId = $_GET['id'];
$email = $_SESSION['userLoggedIn'];

// Get citizen_id from email
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$email]);
$citizen_id = $query->fetchColumn();

if (!$citizen_id) {
    die("User not found.");
}

// Verify issue belongs to this citizen
$query = $con->prepare("SELECT photo_path FROM issues WHERE id = ? AND citizen_id = ?");
$query->execute([$issueId, $citizen_id]);
$issue = $query->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found or access denied.");
}

// Delete the image file if it exists
if (!empty($issue['photo_path'])) {
    $imagePath = 'C:/xampp/htdocs/3Bit/' . $issue['photo_path'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete the issue from database
$delete = $con->prepare("DELETE FROM issues WHERE id = ? AND citizen_id = ?");
$delete->execute([$issueId, $citizen_id]);

// Redirect with optional success message
header("Location: issues.php");
exit();
?>
