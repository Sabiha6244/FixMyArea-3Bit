<?php
// dashboard/provider.php
session_start();
require_once("../includes/config.php");

// Check if user is logged in and is an admin
$userEmail = $_SESSION["userLoggedIn"] ?? null;
$userRole = null;

if ($userEmail) {
    $query = $con->prepare("SELECT role FROM users WHERE email = ?");
    $query->execute([$userEmail]);
    $userRole = $query->fetchColumn();
}

if ($userRole !== 'service_provider') {
    header("Location: ../index.php");
    exit();
}

// Get user ID
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$user_id = $stmt->fetchColumn();

// Check if provider profile exists
$stmt = $con->prepare("SELECT * FROM service_providers WHERE user_id = ?");
$stmt->execute([$user_id]);
$providerProfile = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch assigned service requests using PDO
$sql = "SELECT sr.*, i.title, i.description, i.photo_path, i.location
        FROM service_requests sr
        JOIN issues i ON sr.issue_id = i.id
        WHERE sr.provider_id = ?";
$jobStmt = $con->prepare($sql);
$jobStmt->execute([$user_id]);
$jobs = $jobStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Provider Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color:rgb(81, 81, 93);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        header {
            background:rgb(39, 39, 45);
            padding: 10px 10px;
            border-radius: 40px;
            padding: 30px;
            width: 20%;
        }
        header a {
            color: #fff;
            text-decoration: none;
            background: #4c4cff;
            padding: 10px 14px;
            border-radius: 5px;
        }
        h1, h2 {
            color:whitesmoke;
        }
        .container {
            padding: 20px 30px;
        }
        .card {
            background:rgb(33, 33, 36);
            border-radius: 10px;
            padding: 30px;
            width: 60%;
            margin-bottom: 20px;
            
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .card img {
            max-width: 50%;
            height: auto;
            
            border-radius: 8px;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background:rgb(66, 66, 73);
            color: #fff;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Provider Dashboard</h1>
    </header>

    <div class="container">
        <?php if (!$providerProfile): ?>
            <div class="card">
                <h2>Complete Your Provider Profile</h2>
                <p>You have not submitted your provider details yet.</p>
                <a href="../register_provider.php" class="btn">Fill Provider Info</a>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>Welcome, <?= htmlspecialchars($providerProfile['company_name']) ?></h2>
                <p>Service: <?= htmlspecialchars($providerProfile['service_type']) ?> | Area: <?= htmlspecialchars($providerProfile['service_area']) ?></p>
                <p>Status: <?= $providerProfile['verified'] ? "✅ Verified" : "⏳ Awaiting Verification" ?></p>
            </div>
        <?php endif; ?>

        <h2>Assigned Jobs</h2>
        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    <?php if ($job['photo_path']): ?>
                        <img src="/3Bit/<?= htmlspecialchars($job['photo_path']) ?>" alt="Issue image">
                    <?php endif; ?>
                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($job['status']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p>No jobs assigned to you yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
