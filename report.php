<?php
// report.php - Report an Issue Page
session_start();
require 'C:/xampp/htdocs/3Bit/includes/config.php';


// Redirect if user is not logged in
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];

// Fetch the citizen ID securely
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$email]);
$citizen_id = $query->fetchColumn();

if (!$citizen_id) {
    die("Error: Citizen ID not found.");
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue - FixMyArea</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <header>
        <div class="container">
            <h1>Report an Issue</h1>
            <nav>
                <ul>
                    
                    <li><a href="issues.php">My Issues</a></li>
                    <li><a href="report.php" class="active">Report an Issue</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section>
            <h2>Submit a New Issue</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message"><?= htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>

            <form action="../api/report_issue.php" method="post" enctype="multipart/form-data" class="form-style">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="citizen_id" value="<?= htmlspecialchars($citizen_id); ?>">

                <label for="description">Issue Description:</label>
                <textarea name="description" id="description" placeholder="Provide details about the issue..." required></textarea>

                <label for="photo">Upload an Image (JPEG, PNG, max 2MB):</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg, image/png" required>

                <button type="submit" class="btn-primary">Submit Report</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date("Y"); ?> FixMyArea | Inspired by FixMyStreet</p>
        </div>
    </footer>
</body>
</html>
