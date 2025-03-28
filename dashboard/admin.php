<?php
// dashboard/admin.php
session_start();
//require '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <a href="../logout.php">Logout</a>
    </header>
    <section>
        <h2>Manage Reports</h2>
        <p>List of reported issues will appear here.</p>
    </section>
</body>
</html>
