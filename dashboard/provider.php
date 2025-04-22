<?php
session_start();
require_once("../includes/config.php");

$userEmail = $_SESSION["userLoggedIn"] ?? null;

if (!$userEmail) {
    header("Location: ../index.php");
    exit();
}

// Check user role
$query = $con->prepare("SELECT id, role FROM users WHERE email = ?");
$query->execute([$userEmail]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'service_provider') {
    header("Location: ../index.php");
    exit();
}

$user_id = $user['id'];

// Get provider info
$stmt = $con->prepare("SELECT * FROM service_providers WHERE user_id = ?");
$stmt->execute([$user_id]);
$providerProfile = $stmt->fetch(PDO::FETCH_ASSOC);

// Get assigned jobs
$sql = "SELECT sr.*, i.title, i.description, i.photo_path, i.location, sr.issue_id, sr.admin_id
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
    <title>Provider Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #2f2f39;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background: #1e1e25;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        header h1 {
            margin: 0;
            color: #fff;
        }

        .container {
            padding: 30px 50px;
        }

        .card {
            background: #24242c;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
        }

        .card img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 15px;
        }

        .btn {
            background-color: #4c4cff;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #3b3bd1;
        }

        h2,
        h3 {
            margin-top: 0;
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
                <p>Please complete your service provider information to begin receiving tasks.</p>
                <a href="../register_provider.php" class="btn">Fill Provider Info</a>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>Welcome, <?= htmlspecialchars($providerProfile['company_name']) ?></h2>
                <p><strong>Service:</strong> <?= htmlspecialchars($providerProfile['service_type']) ?> <br>
                    <strong>Area:</strong> <?= htmlspecialchars($providerProfile['service_area']) ?> <br>
                    <strong>Status:</strong> <?= $providerProfile['verified'] ? "✅ Verified" : "⏳ Pending Verification" ?>
                </p>
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
                    <a class="btn" href="../chat.php?sender_id=<?= $user_id ?>&receiver_id=<?= $job['admin_id'] ?>&issue_id=<?= $job['issue_id'] ?>">
                        Chat with Admin
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p>No jobs have been assigned to you yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>