<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get issue ID from query
$issueId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT issues.*, users.name AS reporter_name 
        FROM issues 
        JOIN users ON issues.citizen_id = users.id 
        WHERE issues.id = $issueId";
$sql = "SELECT issues.*, 
        users.name AS reporter_name,
        ST_Y(issues.location) AS latitude, 
        ST_X(issues.location) AS longitude
 FROM issues 
 JOIN users ON issues.citizen_id = users.id 
 WHERE issues.id = $issueId";


$result = $conn->query($sql);
$issue = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Report - FixMyArea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2e2e2e;
            color: whitesmoke;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #555;
            color: white;
            padding: 20px;
        }

        .sidebar .logo {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar nav a {
            display: block;
            padding: 12px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar nav a:hover {
            background-color: #34495e;
        }

        .main {
            margin-left: 280px;
            padding: 20px;
        }

        .card {
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .map-container {
            height: 300px;
            border: 2px solid #888;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn-custom {
            background-color:rgb(31, 31, 32);
            border: none;
            color: white;
        }

        .btn-custom:hover {
            background-color:rgb(54, 56, 57);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="../assets/images/logo.png" alt="Logo" style="width: 100%; margin-bottom: 20px;">
        <nav>
            <a href="../index.php">Home</a>
            <a href="../report.php">Report an Issue</a>
            <a href="../track.php">Track Issues</a>
            <a href="admin.php">Admin Dashboard</a>
            <a href="../logout.php">Logout</a>
            <p style="margin-top: 100px; font-size: 0.9rem;">© 2025 FixMyArea</p>
        </nav>
    </div>

    <div class="main">
        <h2>Issue Report Details</h2>
        <hr>

        <?php if ($issue): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($issue['title']); ?></h3>
                <p><strong>Reported By:</strong> <?php echo htmlspecialchars($issue['reporter_name']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($issue['status']); ?></p>
                <p><strong>Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($issue['created_at'])); ?></p>
                <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>

                <?php if (!empty($issue['photo_path'])): ?>
                    <img src="/3Bit/<?php echo htmlspecialchars($issue['photo_path']); ?>" alt="Issue Image">
                <?php endif; ?>

                <?php if (!empty($issue['latitude']) && !empty($issue['longitude'])): ?>
                    <div id="map" class="map-container"></div>
                <?php endif; ?>

                <a href="admin.php" class="btn btn-custom mt-3">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <p>Issue not found.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($issue['latitude']) && !empty($issue['longitude'])): ?>
        <!-- Include OpenLayers for Map -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css" />
        <script src="https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js"></script>
        <script>
            var map = new ol.Map({
                target: 'map',
                layers: [new ol.layer.Tile({
                    source: new ol.source.OSM()
                })],
                view: new ol.View({
                    center: ol.proj.fromLonLat([<?php echo $issue['longitude']; ?>, <?php echo $issue['latitude']; ?>]),
                    zoom: 15
                })
            });

            var marker = new ol.Feature({
                geometry: new ol.geom.Point(ol.proj.fromLonLat([<?php echo $issue['longitude']; ?>, <?php echo $issue['latitude']; ?>]))
            });

            var vectorLayer = new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: [marker]
                })
            });

            map.addLayer(vectorLayer);
        </script>
    <?php endif; ?>

</body>

</html>

<?php $conn->close(); ?>