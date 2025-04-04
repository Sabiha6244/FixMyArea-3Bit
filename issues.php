<?php
// issues.php - My Issues Dashboard

session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';

// Redirect if user is not logged in
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];

// Fetch citizen_id securely
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$email]);
$citizen_id = $query->fetchColumn();

if (!$citizen_id) {
    die("Error: Citizen ID not found.");
}

// Fetch issues reported by the logged-in citizen
$query = $con->prepare("SELECT description, status, created_at FROM issues WHERE citizen_id = ? ORDER BY created_at DESC");
$query->execute([$citizen_id]);
$issues = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Issues - FixMyArea</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <header>
        <div class="container">
            <h1>My Reported Issues</h1>
            <nav>
                <ul>
                
                <li><a href="issues.php" class="active">My Issues</a></li>
                    <li><a href="report.php">Report an Issue</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section>
            <h2>Reported Issues</h2>
            <?php if (!empty($issues)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td><?= htmlspecialchars($issue['description']); ?></td>
                                <td><?= htmlspecialchars($issue['status']); ?></td>
                                <td><?= date("d M Y, H:i", strtotime($issue['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No issues reported yet. <a href="report.php">Report one now!</a></p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date("Y"); ?> FixMyArea | Inspired by FixMyStreet</p>
        </div>
    </footer>
</body>
</html>
