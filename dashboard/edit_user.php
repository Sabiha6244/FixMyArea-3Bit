<?php
// admin/edit_user.php

session_start();
require_once("../includes/config.php");

// Check if user is an admin
$userEmail = $_SESSION["userLoggedIn"] ?? null;
if (!$userEmail) {
    header("Location: ../login.php");
    exit;
}

// Verify admin role
$query = $con->prepare("SELECT role FROM users WHERE email = ?");
$query->execute([$userEmail]);
$role = $query->fetchColumn();

if ($role !== 'admin') {
    echo "Access denied.";
    exit;
}

// Get user ID from URL
$userId = $_GET['id'] ?? null;

if (!$userId) {
    header("Location: manage_users.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $userRole = $_POST['role'] ?? 'citizen';

    $updateQuery = $con->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $updateQuery->execute([$name, $email, $userRole, $userId]);

    header("Location: manage_users.php?success=User updated");
    exit;
}

// Fetch existing user data
$selectQuery = $con->prepare("SELECT * FROM users WHERE id = ?");
$selectQuery->execute([$userId]);
$user = $selectQuery->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - FixMyArea Admin</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>
    <div class="main-content" style="padding: 20px; max-width: 600px; margin: auto;">
        <h2>Edit User</h2>
        <form method="POST">
            <label>Name:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label>Role:</label><br>
            <select name="role">
                <option value="citizen" <?= $user['role'] === 'citizen' ? 'selected' : '' ?>>Citizen</option>
                <option value="service_provider" <?= $user['role'] === 'service_provider' ? 'selected' : '' ?>>Service Provider</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select><br><br>

            <button type="submit" class="btn">Update User</button>
            <a href="manage_users.php" class="btn" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
</body>
</html>
