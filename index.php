<?php
// index.php
// Homepage
session_start();
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
        <h1>Welcome to the Community Issue Tracker</h1>
        <nav>
            <?php if (isset($_SESSION["userLoggedIn"])): ?>
                <?php
                require_once("includes/config.php");
                require_once("includes/classes/Account.php");
                $account = new Account($con);
                $userEmail = $_SESSION["userLoggedIn"];
                $role = $account->getUserRole($userEmail);
                ?>
                <?php if ($role == 'citizen'): ?>
                    <a href="dashboard/citizen.php">Citizen Dashboard</a>
                <?php elseif ($role == 'service_provider'): ?>
                    <a href="dashboard/provider.php">Service Provider Dashboard</a>
                <?php elseif ($role == 'admin'): ?>
                    <a href="dashboard/admin.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <section>
        <p>Report and track community issues, hire service providers, and manage civic services efficiently.</p>
    </section>
</body>
</html>
