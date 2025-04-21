<?php 
session_start();
include 'includes/config.php';

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];

// Utility function to get name from JSON by ID
function getNameFromJson($jsonPath, $idField, $nameField, $selectedId, $rootKey = null) {
    $json = json_decode(file_get_contents($jsonPath), true);

    // If there's a root key like 'districts' or 'divisions', use it
    $data = $rootKey ? $json[$rootKey] : $json;

    foreach ($data as $item) {
        if ($item[$idField] == $selectedId) {
            return $item[$nameField];
        }
    }

    return $selectedId; // fallback to ID if not found
}


// Get posted IDs
$divisionId = $_POST['division'] ?? '';
$districtId = $_POST['district'] ?? '';
$cityId     = $_POST['city'] ?? '';
$upazilaId  = $_POST['upazila'] ?? '';

$division = getNameFromJson('includes/bangladesh_geojson/bd-divisions.json', 'id', 'name', $divisionId, 'divisions');
$district = getNameFromJson('includes/bangladesh_geojson/bd-districts.json', 'id', 'name', $districtId, 'districts');
$city = getNameFromJson('includes/bangladesh_geojson/dhaka-city.json', 'id', 'name', $cityId); // no rootKey if it's a flat array
$upazila = getNameFromJson('includes/bangladesh_geojson/bd-upazilas.json', 'id', 'name', $upazilaId); // same here

// Handle profile picture upload
$profile_picture_path = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['profile_picture']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        $profile_picture_path = $targetPath;
    }
}

// Build query dynamically
$query = "UPDATE users SET 
    division = ?, 
    district = ?, 
    city_corporation = ?, 
    upazila = ?, 
    postcode = ?, 
    
    updated_at = NOW()";

$params = [$division, $district, $city, $upazila, $postcode];

if ($profile_picture_path) {
    $query .= ", profile_picture = ?";
    $params[] = $profile_picture_path;
}

$query .= " WHERE email = ?";
$params[] = $email;

$stmt = $con->prepare($query);
$stmt->execute($params);

// Redirect to profile page
header("Location: profile.php");
exit;
?>
