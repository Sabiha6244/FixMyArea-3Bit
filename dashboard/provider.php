<?php
// dashboard/provider.php
session_start();
//require '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Provider') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Service Provider Dashboard</h1>
        <a href="../logout.php">Logout</a>
    </header>
    <section>
        <h2>Available Jobs</h2>
        <p>List of assigned jobs will appear here.</p>
    </section>
</body>
</html>
