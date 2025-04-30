<?php
session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];

// CSRF token check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

// Get citizen ID
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$email]);
$citizen_id = $query->fetchColumn();
if (!$citizen_id) {
    die("User not found.");
}

// Sanitize and validate inputs
$issue_id    = $_POST['issue_id'] ?? '';
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category    = trim($_POST['category'] ?? '');
$latitude    = $_POST['latitude'] ?? null;
$longitude   = $_POST['longitude'] ?? null;

if (!is_numeric($issue_id) || empty($title) || empty($description) || empty($category)) {
    die("Invalid form data.");
}

// Validate geolocation
if (!is_numeric($latitude) || !is_numeric($longitude)) {
    die("Invalid geolocation values.");
}

// Check ownership & get current photo
$query = $con->prepare("SELECT photo_path FROM issues WHERE id = ? AND citizen_id = ?");
$query->execute([$issue_id, $citizen_id]);
$issue = $query->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found or access denied.");
}

$photo_path = $issue['photo_path']; // keep old photo unless replaced

// Handle new image upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $allowed_exts = ['jpg', 'jpeg', 'png'];
    $upload_dir = "uploads/";
    $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_exts)) {
        $new_filename = uniqid('issue_') . '.' . $file_ext;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], "../3Bit/" . $target_file)) {
            // Delete old image
            if (!empty($photo_path) && file_exists("../3Bit/" . $photo_path)) {
                unlink("../3Bit/" . $photo_path);
            }
            $photo_path = $target_file;
        } else {
            die("Image upload failed.");
        }
    } else {
        die("Invalid file type. Only JPG, JPEG, and PNG allowed.");
    }
}

// Prepare SQL with GEOMETRY location update
$sql = "UPDATE issues 
        SET title = ?, description = ?, category = ?, photo_path = ?, location = ST_GeomFromText(?), updated_at = NOW()
        WHERE id = ? AND citizen_id = ?";

$point = "POINT($longitude $latitude)";

$update = $con->prepare($sql);
$success = $update->execute([
    $title,
    $description,
    $category,
    $photo_path,
    $point,
    $issue_id,
    $citizen_id
]);

if ($success) {
    header("Location: issues.php?msg=updated");
    exit();
} else {
    die("Failed to update issue.");
}
?>
