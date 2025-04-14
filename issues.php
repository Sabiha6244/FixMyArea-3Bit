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

$query = $con->prepare("SELECT id, description, status, created_at, photo_path FROM issues WHERE citizen_id = ? ORDER BY created_at DESC");
$query->execute([$citizen_id]);
$issues = $query->fetchAll(PDO::FETCH_ASSOC);

// Group issues by month/year
$groupedIssues = [];
foreach ($issues as $issue) {
    $monthYear = date("F Y", strtotime($issue['created_at']));
    $groupedIssues[$monthYear][] = $issue;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Issues - FixMyArea</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body class="dashboard">
    <header>
        <div class="container">
            <h1>My Reported Issues</h1>
            <nav>
                <ul>
                    <li><a href="issues.php" class="active">My Issues</a></li>
                    <li><a href="report.php">Report an Issue</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section>
            <h2>Reported Issues</h2>

            <?php if (!empty($groupedIssues)): ?>
                <?php foreach ($groupedIssues as $monthYear => $monthlyIssues): ?>
                    <div class="issue-group">
                        <h2 class="month-title"><?= htmlspecialchars($monthYear); ?></h2>
                        <?php foreach ($monthlyIssues as $issue): ?>
                            <div class="issue-card">
                                <h3><?= htmlspecialchars($issue['description']); ?></h3>
                                <p><strong>Status:</strong> <?= htmlspecialchars($issue['status']); ?></p>
                                <p><strong>Reported on:</strong> <?= date("d M Y, H:i", strtotime($issue['created_at'])); ?></p>

                                <?php if (!empty($issue['photo_path'])): ?>
                                    <div class="issue-image">
                                        <a href="#popup<?= $issue['id']; ?>">
                                            <img src="/3Bit/<?= htmlspecialchars($issue['photo_path']); ?>" alt="Issue Image" class="issue-thumbnail">
                                        </a>
                                    </div>

                                    <!-- Fullscreen popup -->
                                    <div id="popup<?= $issue['id']; ?>" class="popup" onclick="this.style.display='none'">
                                        <img src="/3Bit/<?= htmlspecialchars($issue['photo_path']); ?>" alt="Full Image">
                                    </div>
                                <?php endif; ?>

                                <div class="issue-actions">
                                    <button onclick="editIssue(<?= $issue['id']; ?>)">Edit</button>
                                    <button onclick="deleteIssue(<?= $issue['id']; ?>)">Delete</button>
                                    <button onclick="reportAgain(<?= $issue['id']; ?>)">Report Again</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-issues">
                    <p>No issues reported yet. <a href="report.php">Report one now!</a></p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date("Y"); ?> FixMyArea </p>
        </div>
    </footer>

    <script>
        function editIssue(id) {
            window.location.href = `edit_issue.php?id=${id}`;
        }

        function deleteIssue(id) {
            if (confirm("Are you sure you want to delete this issue?")) {
                window.location.href = `delete_issue.php?id=${id}`;
            }
        }

        function reportAgain(id) {
            window.location.href = `report.php?ref_id=${id}`;
        }
    </script>
</body>

</html>
