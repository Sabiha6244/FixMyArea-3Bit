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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid issue ID.");
}

$issue_id = $_GET['id'];

$query = $con->prepare("SELECT *, ST_AsText(location) as location_text FROM issues WHERE id = ? AND citizen_id = ?");
$query->execute([$issue_id, $citizen_id]);
$issue = $query->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found or access denied.");
}

// Extract latitude & longitude from POINT(lon lat)
preg_match('/POINT\(([-\d\.]+) ([-\d\.]+)\)/', $issue['location_text'], $matches);
$longitude = $matches[1] ?? '';
$latitude = $matches[2] ?? '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Issue - FixMyArea</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css">
    <style>
        #map {
            width: 100%;
            height: 300px;
            margin: 1em 0;
            border: 2px solid #ccc;
        }
    </style>
</head>

<body class="dashboard">

<header>
    <div class="container header-container">
        <h1 class="site-title">FixMyArea</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="issues.php">My Issues</a></li>
                <li><a href="report.php">Report an Issue</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
    <section class="report-section">
        <h2>Edit Issue</h2>

        <form action="update_issue.php" method="post" enctype="multipart/form-data" class="report-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="issue_id" value="<?= htmlspecialchars($issue['id']); ?>">

            <label for="category">Issue Category:</label>
            <select name="category" id="category" required>
                <option value="Road Repair" <?= $issue['category'] == "Road Repair" ? "selected" : "" ?>>Road Repair</option>
                <option value="Sanitation" <?= $issue['category'] == "Sanitation" ? "selected" : "" ?>>Sanitation</option>
                <option value="Utilities" <?= $issue['category'] == "Utilities" ? "selected" : "" ?>>Utilities</option>
                <option value="Public Safety" <?= $issue['category'] == "Public Safety" ? "selected" : "" ?>>Public Safety</option>
            </select>

            <label for="title">Issue Title:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($issue['title']); ?>" required>

            <label for="description">Issue Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($issue['description']); ?></textarea>

            <?php if (!empty($issue['photo_path'])): ?>
                <label>Current Image:</label>
                <div class="issue-image">
                    <img src="/3Bit/<?= htmlspecialchars($issue['photo_path']); ?>" alt="Current Image" class="issue-thumbnail">
                </div>
            <?php endif; ?>

            <label for="photo">Change Image (optional):</label>
            <input type="file" name="photo" id="photo" accept="image/jpeg, image/png">

            <label for="map">Update Location:</label>
            <div id="map"></div>

            <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($latitude); ?>">
            <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($longitude); ?>">

            <button type="submit" class="btn-primary">Update Issue</button>
        </form>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date("Y"); ?> FixMyArea</p>
    </div>
</footer>

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

        // Update hidden inputs
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        // Move marker
        marker.setGeometry(new ol.geom.Point(event.coordinate));
    });
</script>

</body>
</html>
