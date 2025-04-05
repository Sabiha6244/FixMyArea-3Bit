<?php
// index.php - FixMyArea Homepage

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
    <title>FixMyArea - Community Issue Tracker</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="assets/images/logo.png" title="Logo" alt="Site Logo" />
        <nav>
            <a href="index.php">Home</a>
            <a href="report.php">Report an Issue</a>
            <a href="issues.php">Track Issues</a>

            <?php if ($userRole): ?>
                <?php if ($userRole === 'citizen'): ?>
                    <a href="dashboard/citizen.php">Citizen Dashboard</a>
                <?php elseif ($userRole === 'service_provider'): ?>
                    <a href="dashboard/provider.php">Provider Dashboard</a>
                <?php elseif ($userRole === 'admin'): ?>
                    <a href="dashboard/admin.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
        <div class="bottom-text">© <?= date("Y") ?> FixMyArea</div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <section class="hero">
            <div class="container">
                <h2>Report, view, or discuss local problems</h2>
                <h3>(like potholes, streetlight outages, trash collection problems, and more!)</h3>
                <p> Citizens face difficulties reporting local issues like broken streetlights, 
                    potholes, or garbage disposal. Often, they don’t know where to report, 
                    and the lack of updates makes them feel ignored. Additionally, authorities 
                    struggle to assign service providers and track issue resolution efficiently.
                    FixMyArea does send your reports directly to your local council, 
                    or whichever other authority is responsible for dealing with a problem.
                    We wanted to make it easier to report problems in your community, 
                    even if you don’t know who those reports should go to.
                    So we made FixMyArea. 
                    All you have to do is type in a BD postcode – 
                    or let the site locate you automatically – 
                    and describe your problem. 
                    Then we send your report to the people whose job it is to fix it.

                    We also publish them online, so that others in the community can see 
                    what’s already been reported and subscribe to any reports they’re interested in.
                    </p>
                <a href="report.php" class="btn">Report an Issue</a>
            </div>
        </section>

        <section class="search-bar">
            <div class="container">
                <input type="text" id="searchBox" placeholder="Search issues (e.g., potholes, streetlight)">
                <button onclick="searchIssues()">Search</button>
            </div>
        </section>

        <section class="latest-reports">
            <div class="container">
                <h4>Latest Reported Issues</h4>
                <ul id="latestIssues">
                    <?php
                    $query = $con->query("SELECT id, title, category, status FROM issues ORDER BY created_at DESC LIMIT 5");
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                        <li>
                            <strong>
                                <a href="issues.php?id=<?= htmlspecialchars($row['id']) ?>">
                                    <?= htmlspecialchars($row['title']) ?>
                                </a>
                            </strong> - 
                            <?= htmlspecialchars($row['category']) ?>
                            <span class="status <?= strtolower($row['status']) ?>">
                                (<?= htmlspecialchars($row['status']) ?>)
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </section>

        <footer>
            <div class="container">
                <p>&copy; <?= date("Y") ?> FixMyArea </p>
            </div>
        </footer>
    </div>

    <script>
        function searchIssues() {
            const query = document.getElementById("searchBox").value.trim();
            if (query) {
                window.location.href = "issues.php?search=" + encodeURIComponent(query);
            }
        }
    </script>
</body>
</html>
