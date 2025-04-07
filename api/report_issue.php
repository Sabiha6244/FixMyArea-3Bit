<?php
session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../report.php?error=Invalid request");
    exit();
}

// CSRF token check
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: ../report.php?error=Invalid CSRF token");
    exit();
}

// Sanitize and validate input
$citizen_id = filter_input(INPUT_POST, 'citizen_id', FILTER_SANITIZE_NUMBER_INT);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;

if (!$citizen_id || !$title || !$description || !$category || !$latitude || !$longitude) {
    header("Location: ../report.php?error=Missing required fields");
    exit();
}

// Ensure latitude and longitude are valid floats
if (!is_numeric($latitude) || !is_numeric($longitude)) {
    header("Location: ../report.php?error=Invalid location data");
    exit();
}

// Handle image upload
$photoPath = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'C:/xampp/htdocs/3Bit/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmp = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    // Validate file extension
    if (!in_array($fileExt, $allowed)) {
        header("Location: ../report.php?error=Invalid image format");
        exit();
    }

    // Validate file size
    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        header("Location: ../report.php?error=Image exceeds 2MB limit");
        exit();
    }

    // Generate safe unique filename
    $newFileName = uniqid("img_", true) . '.' . $fileExt;
    $filePath = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmp, $filePath)) {
        header("Location: ../report.php?error=Failed to upload image");
        exit();
    }

    // Save relative path for DB
    $photoPath = 'uploads/' . $newFileName;
}

try {
    // Prepare insert query with spatial point
    $stmt = $con->prepare("
        INSERT INTO issues 
        (citizen_id, title, description, category, location, photo_path, status) 
        VALUES (?, ?, ?, ?, ST_GeomFromText(?), ?, 'Pending')
    ");

    $locationPoint = sprintf("POINT(%f %f)", $longitude, $latitude);

    $stmt->execute([
        $citizen_id,
        $title,
        $description,
        $category,
        $locationPoint,
        $photoPath
    ]);

    header("Location: ../report.php?success=1");
    exit();

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    header("Location: ../report.php?error=Database error");
    exit();
}
?>
