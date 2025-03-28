<?php
// issues.php - My Issues Dashboard
session_start();
require dirname(__DIR__) . '/includes/config.php';
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}
$email = $_SESSION['userLoggedIn'];
$query = $con->prepare("SELECT * FROM issues WHERE citizen_id = ? ORDER BY created_at DESC");
$query->execute([$email]);
$issues = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en-gb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, width=device-width, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My Issues - FixMyStreet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <header>
        <div class="container">
            <h1>My Reported Issues</h1>
            <nav>
                <ul>
                    <li><a href="citizen.php">Home</a></li>
                    <li><a href="issues.php">My Issues</a></li>
                    <li><a href="report.php">Report an Issue</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <section>
            <h2>Reported Issues</h2>
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
                            <td><?php echo htmlspecialchars($issue['description']); ?></td>
                            <td><?php echo htmlspecialchars($issue['status']); ?></td>
                            <td><?php echo htmlspecialchars($issue['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>