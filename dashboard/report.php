<?php
// report.php - Report an Issue Dashboard
session_start();
require dirname(__DIR__) . '/includes/config.php';
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en-gb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, width=device-width, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Report an Issue - FixMyStreet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard">
    <header>
        <div class="container">
            <h1>Report an Issue</h1>
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
            <h2>Submit a New Issue</h2>
            <form action="../api/report_issue.php" method="post" enctype="multipart/form-data" class="form-style">
                <label for="description">Issue Description:</label>
                <textarea name="description" id="description" placeholder="Provide details about the issue..." required></textarea>
                <label for="photo">Upload an Image:</label>
                <input type="file" name="photo" id="photo" required>
                <button type="submit" class="btn-primary">Submit Report</button>
            </form>
        </section>
    </main>
</body>

