<?php
// dashboard/admin.php

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

if ($userRole !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get admin statistics
$totalUsers = $con->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalIssues = $con->query("SELECT COUNT(*) FROM issues")->fetchColumn();
$totalProviders = $con->query("SELECT COUNT(*) FROM service_providers")->fetchColumn();
$pendingIssues = $con->query("SELECT COUNT(*) FROM issues WHERE status = 'Pending'")->fetchColumn();
$resolvedIssues = $con->query("SELECT COUNT(*) FROM issues WHERE status = 'Resolved'")->fetchColumn();
$inProgressIssues = $con->query("SELECT COUNT(*) FROM issues WHERE status = 'In Progress'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - FixMyArea</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        .dashboard {
            padding: 2rem;
        }

        .dashboard h2 {
            margin-bottom: 4rem;
            color: whitesmoke;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background-color: darkgrey;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .dashboard h3{
            margin-bottom: 2rem;
        }

        .stat-card h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .stat-card span {
            font-size: 4rem;
            font-weight: bold;
            color:#333;
        }

        .quick-links {
            margin-top: 2.5rem;
        }

        .quick-links a {
            display: inline-block;
            margin-right: 3rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.6rem;
            background-color:rgb(3, 8, 14);
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .quick-links a:hover {
            background-color:rgb(113, 117, 121);
        }

        .logout {
            margin-top: 3rem;
        }

        .logout a {
            color: whitesmoke;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="../assets/images/logo.png" alt="Logo" />
        <nav>
            <a href="../index.php">Home</a>
            <a href="../report.php">Report an Issue</a>
            <a href="../issues.php">Track Issues</a>
            <a href="admin.php">Admin Dashboard</a>
            <a href="../logout.php">Logout</a>
        </nav>
        <div class="bottom-text">© <?= date("Y") ?> FixMyArea</div>
    </div>

    <div class="main-content">
        <div class="dashboard">
            <h2>Welcome, Admin!</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <span><?= $totalUsers ?></span>
                </div>
                <div class="stat-card">
                    <h3>Total Issues</h3>
                    <span><?= $totalIssues ?></span>
                </div>
                <div class="stat-card">
                    <h3>Total Service Providers</h3>
                    <span><?= $totalProviders ?></span>
                </div>
                <div class="stat-card">
                    <h3>Pending Issues</h3>
                    <span><?= $pendingIssues ?></span>
                </div>
                <div class="stat-card">
                    <h3>In Progress</h3>
                    <span><?= $inProgressIssues ?></span>
                </div>
                <div class="stat-card">
                    <h3>Resolved Issues</h3>
                    <span><?= $resolvedIssues ?></span>
                </div>
            </div>

            <div class="quick-links">
                <h3>Quick Links</h3>
                <a href="../dashboard/manage_users.php">Manage Users</a>
                <a href="../dashboard/manage_issues.php">Manage Issues</a>
                <a href="../dashboard/manage_providers.php">Manage Providers</a>
            </div>

            <div class="logout">
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>
