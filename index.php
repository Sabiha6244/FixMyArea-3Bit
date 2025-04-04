<?php
// index.php - Community Issue Tracker Homepage

session_start();
require_once("includes/config.php");

// Check if user is logged in and get role
$userEmail = $_SESSION["userLoggedIn"] ?? null;
$userRole = null;

if ($userEmail) {
    $query = $con->prepare("SELECT role FROM users WHERE email = ?");
    $query->execute([$userEmail]);
    $userRole = $query->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Issue Tracker</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Community Issue Tracker</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="report.php">Report an Issue</a>
                <a href="issues.php">Track Issues</a>
                
                <?php if ($userRole): ?>
                    <div class="dropdown">
                        <button class="dropbtn">My Account</button>
                        <div class="dropdown-content">
                            <?php if ($userRole === 'citizen'): ?>
                                <a href="dashboard/citizen.php">Citizen Dashboard</a>
                            <?php elseif ($userRole === 'service_provider'): ?>
                                <a href="dashboard/provider.php">Service Provider Dashboard</a>
                            <?php elseif ($userRole === 'admin'): ?>
                                <a href="dashboard/admin.php">Admin Dashboard</a>
                            <?php endif; ?>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Report local issues & help improve your community</h2>
            <p>Use our platform to report potholes, streetlight outages, garbage collection issues, and more.</p>
            <a href="report.php" class="btn">Report an Issue</a>
        </div>
    </section>

    <section class="search-bar">
        <div class="container">
            <input type="text" id="searchBox" placeholder="Search for issues (e.g., potholes, streetlight)">
            <button onclick="searchIssues()">Search</button>
        </div>
    </section>

    <section class="latest-reports">
        <div class="container">
            <h3>Latest Reported Issues</h3>
            <ul id="latestIssues">
                <?php
                $query = $con->query("SELECT title, category, status FROM issues ORDER BY created_at DESC LIMIT 5");

                while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                    <li>
                        <strong><?= htmlspecialchars($row['title']) ?></strong> - 
                        <?= htmlspecialchars($row['category']) ?> 
                        (Status: <?= htmlspecialchars($row['status']) ?>)
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; <?= date("Y") ?> Community Issue Tracker | Inspired by FixMyStreet</p>
        </div>
    </footer>

    <script>
        function searchIssues() {
            let query = document.getElementById("searchBox").value;
            window.location.href = "issues.php?search=" + encodeURIComponent(query);
        }
    </script>
</body>
</html>
