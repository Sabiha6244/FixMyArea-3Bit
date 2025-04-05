<?php
session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$email]);
$citizen_id = $query->fetchColumn();

if (!$citizen_id) {
    die("Error: Citizen ID not found.");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report an Issue - FixMyArea</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/style.css">

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY" async defer></script>
</head>
<body class="dashboard">

<header>
    <div class="container header-container">
        <h1 class="site-title">FixMyArea</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="issues.php">My Issues</a></li>
                <li><a href="report.php" class="active">Report an Issue</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
    <section class="report-section">
        <h2>Submit a New Issue</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?= htmlspecialchars($_GET['error']); ?></p>
        <?php elseif (isset($_GET['success'])): ?>
            <p class="success-message">Your issue has been successfully reported!</p>
        <?php endif; ?>

        <form action="../api/report_issue.php" method="post" enctype="multipart/form-data" class="report-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="citizen_id" value="<?= htmlspecialchars($citizen_id); ?>">

            <!-- Category Selection -->
            <label for="category">Issue Category:</label>
            <select name="category" id="category" required>
                <option value="" disabled selected>Select a category</option>
                <option value="Road">Road</option>
                <option value="Garbage">Garbage</option>
                <option value="Streetlight">Streetlight</option>
                <option value="Water">Water</option>
                <option value="Other">Other</option>
            </select>

            <!-- Description -->
            <label for="description">Issue Description:</label>
            <textarea name="description" id="description" placeholder="Describe the issue in detail..." required></textarea>

            <!-- Image Upload -->
            <label for="photo">Upload an Image (JPEG, PNG, max 2MB):</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg, image/png" required>

            <!-- Location Detection -->
            <label for="location">Detected Location:</label>
            <input type="text" id="location" name="location" placeholder="Fetching your location..." readonly required>

            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <!-- Google Map Preview -->
            <div id="map" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>

            <button type="submit" class="btn-primary">Submit Report</button>
        </form>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date("Y"); ?> FixMyArea</p>
    </div>
</footer>

<script>
let map, marker;

window.onload = () => {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);

                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
                document.getElementById("location").value = `${lat}, ${lng}`;

                // Load Google Map
                map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: parseFloat(lat), lng: parseFloat(lng) },
                    zoom: 15
                });

                marker = new google.maps.Marker({
                    position: { lat: parseFloat(lat), lng: parseFloat(lng) },
                    map: map,
                    title: "Your Location"
                });
            },
            (error) => {
                document.getElementById("location").value = "Unable to fetch location.";
            }
        );
    } else {
        document.getElementById("location").value = "Geolocation not supported.";
    }
};
</script>

</body>
</html>
