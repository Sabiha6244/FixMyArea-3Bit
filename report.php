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


    <!-- OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@6.12.0/ol.css" />

    <!-- OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@6.12.0/ol.js"></script>

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

            <form action="api/report_issue.php" method="post" enctype="multipart/form-data" class="report-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="citizen_id" value="<?= htmlspecialchars($citizen_id); ?>">

                <!-- Category Selection -->
                <label for="category">Issue Category:</label>
                <select name="category" id="category" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="Road Repair">Potholes, road damage, sidewalk issues</option>
                    <option value="Sanitation">Garbage collection, public cleanliness</option>
                    <option value="Utilities">Water, electricity, gas issues</option>
                    <option value="Public Safety">Street lights, traffic signals, safety hazards</option>
                </select>

                <!-- Title -->
                <label for="title">Issue Title:</label>
                <input type="text" name="title" id="title" placeholder="e.g., Broken streetlight at 5th Ave" required>

                <!-- Description -->
                <label for="description">Issue Description:</label>
                <textarea name="description" id="description" placeholder="Describe the issue in detail..." required></textarea>

                <!-- Image Upload -->
                <label for="photo">Upload an Image (JPEG, PNG, max 2MB):</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg, image/png" required>

                <!-- Location Detection -->
                <label for="location">Detected Location:</label>
                <div style="margin-bottom: 10px;">
                    <button type="button" class="btn-primary" onclick="detectLocation()">Use My Current Location</button>
                </div>
                <input type="text" id="location" name="location" placeholder="Click to detect location" readonly required>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

                <!-- Map Preview -->
                <div id="map-container" style="display: none; margin-top: 15px;">
                    <div id="map" style="height: 300px; width: 100%;"></div>
                </div>
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

    <script>
        function detectLocation() {
            console.log("Location detection started...");
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    console.log(`Latitude: ${lat}, Longitude: ${lng}`);

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    // Reverse Geocode
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                        const data = await response.json();

                        let address = data.display_name;
                        console.log("Address:", address); // Add logging to see what you get

                        if (data.address) {
                            address = `${data.address.road || ''}, ${data.address.suburb || ''}, ${data.address.city || data.address.town || data.address.village || ''}, ${data.address.postcode || ''}`;
                        }

                        document.getElementById("location").value = address;
                    } catch (err) {
                        console.error(err);
                        document.getElementById("location").value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        alert("Could not fetch address. Showing coordinates instead.");
                    }

                    // Show Map Container before initializing
                    const mapContainer = document.getElementById("map-container");
                    mapContainer.style.display = "block";

                    // Clear any existing map
                    document.getElementById("map").innerHTML = '';

                    // Coordinates for OpenLayers
                    const coords = ol.proj.fromLonLat([lng, lat]);

                    const map = new ol.Map({
                        target: 'map',
                        layers: [
                            new ol.layer.Tile({
                                source: new ol.source.OSM(),
                            })
                        ],
                        view: new ol.View({
                            center: coords,
                            zoom: 15
                        })
                    });

                    const marker = new ol.Feature({
                        geometry: new ol.geom.Point(coords),
                    });

                    const markerStyle = new ol.style.Style({
                        image: new ol.style.Icon({
                            anchor: [0.5, 1],
                            src: 'https://openlayers.org/en/v4.6.5/examples/data/icon.png',
                        }),
                    });

                    marker.setStyle(markerStyle);

                    const vectorSource = new ol.source.Vector({
                        features: [marker]
                    });

                    const markerLayer = new ol.layer.Vector({
                        source: vectorSource
                    });

                    map.addLayer(markerLayer);

                    // Ensure map renders correctly after being made visible
                    setTimeout(() => {
                        map.updateSize();
                    }, 300);

                }, () => {
                    alert("Failed to fetch your location.");
                });
            } else {
                alert("Geolocation not supported by this browser.");
            }
        }
    </script>