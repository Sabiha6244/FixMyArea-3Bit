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

    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@6.12.0/ol.css" />

    <!-- OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@6.12.0/ol.js"></script>

    <style>
    body {
        margin: 0;
        font-family: 'Arial', Helvetica, sans-serif;
        background-color: #000;
        color: #e5e5e5;
    }

    header {
        background-color: #111;
        color: #fff;
        padding: 1rem 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .container {
        width: 90%;
        max-width: 960px;
        margin: 0 auto;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .site-title {
        font-size: 1.8rem;
        margin: 0;
        font-weight: bold;
        color:rgb(227, 219, 214);
    }

    .nav-links {
        list-style: none;
        display: flex;
        gap: 1rem;
    }

    .nav-links a {
        color: #bbb;
        text-decoration: none;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        transition: background-color 0.2s, color 0.2s;
    }

    .nav-links a.active,
    .nav-links a:hover {
        background-color: #333;
        color: #fff;
    }

    main {
        margin: 2rem 0;
    }

    .report-section h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #fff;
    }

    .report-form {
        background-color: #1a1a1a;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.05);
    }

    .report-form label {
        display: block;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #ccc;
    }

    .report-form input[type="text"],
    .report-form input[type="file"],
    .report-form select,
    .report-form textarea {
        width: 100%;
        padding: 0.75rem;
        background-color: #111;
        border: none;
        border-bottom: 2px solid #333;
        color: #fff;
        border-radius: 4px;
        font-size: 15px;
        transition: border-color 0.3s;
    }

    .report-form input:focus,
    .report-form select:focus,
    .report-form textarea:focus {
        border-bottom-color:rgb(79, 77, 76);
        outline: none;
    }

    .report-form textarea {
        min-height: 60px;
        resize: vertical;
    }

    .btn-primary {
        margin-top: 1.5rem;
        padding: 0.8rem 1.5rem;
        background-color:rgb(90, 88, 86);
        color: #fff;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color:rgb(86, 84, 83);
    }

    .error-message {
        background-color:rgb(71, 68, 68);
        color: #fff;
        padding: 0.7rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 14px;
    }

    .success-message {
        background-color: #27ae60;
        color: #fff;
        padding: 0.7rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 14px;
    }

    footer {
        background-color: #111;
        color: #bbb;
        text-align: center;
        padding: 0.5rem 0;
        margin-top: auto;
        font-size: 13px;
    }
</style>


</head>

<body>

<header>
<div class="layout">
    <!-- Sidebar with header, nav, and footer -->
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

        <form action="api/report_issue.php" method="post" enctype="multipart/form-data" class="report-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="citizen_id" value="<?= htmlspecialchars($citizen_id); ?>">

            <label for="category">Issue Category:</label>
            <select name="category" id="category" required>
                <option value="" disabled selected>Select a category</option>
                <option value="Road Repair">Potholes, road damage, sidewalk issues</option>
                <option value="Sanitation">Garbage collection, public cleanliness</option>
                <option value="Utilities">Water, electricity, gas issues</option>
                <option value="Public Safety">Street lights, traffic signals, safety hazards</option>
            </select>

            <label for="title">Issue Title:</label>
            <input type="text" name="title" id="title" placeholder="e.g., Broken streetlight at 5th Ave" required>

            <label for="description">Issue Description:</label>
            <textarea name="description" id="description" placeholder="Describe the issue in detail..." required></textarea>

            <label for="photo">Upload an Image (JPEG, PNG, max 2MB):</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg, image/png" required>

            <label for="location">Detected Location:</label>
            <button type="button" class="btn-primary" onclick="detectLocation()">Use My Current Location</button>
            <input type="text" id="location" name="location" placeholder="Click to detect location">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <div id="map-container" style="display: none; margin-top: 15px;">
                <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
            </div>

            <button type="submit" class="btn-primary">Submit Report</button>
        </form>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date("Y"); ?> FixMyArea</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js"></script>
<script>
    const lon = parseFloat("<?= $longitude ?>");
    const lat = parseFloat("<?= $latitude ?>");

    const view = new ol.View({
        center: ol.proj.fromLonLat([lon, lat]),
        zoom: 15
    });

    const marker = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
    });

    const vectorSource = new ol.source.Vector({
        features: [marker]
    });

    const vectorLayer = new ol.layer.Vector({
        source: vectorSource
    });

    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            }),
            vectorLayer
        ],
        view: view
    });

    map.on('click', function (event) {
        const coords = ol.proj.toLonLat(event.coordinate);
        const lon = coords[0].toFixed(6);
        const lat = coords[1].toFixed(6);

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        marker.setGeometry(new ol.geom.Point(event.coordinate));
    });
</script>

</body>
</html>
