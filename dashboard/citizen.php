<?php
// citizen.php
session_start();
//require '../config.php';
if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!doctype html>
<html lang="en-gb">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, width=device-width, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Citizen Dashboard - FixMyStreet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="frontpage">
    <header>
        <h1>Citizen Dashboard</h1>
        <nav>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Report, view, or discuss local problems</h2>
        <form action="../api/report_issue.php" method="post" enctype="multipart/form-data">
            <label for="description">Issue Description:</label>
            <input type="text" name="description" id="description" placeholder="Describe the issue" required>
            <label for="photo">Upload Image:</label>
            <input type="file" name="photo" id="photo" required>
            <button type="submit">Submit</button>
        </form>
        <section>
            <h2>Recently Reported Issues</h2>
            <ul id="issue-list">
                <!-- Issues will be dynamically loaded here -->
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 FixMyStreet - Community Issue Tracker</p>
    </footer>
</body>
</html>
