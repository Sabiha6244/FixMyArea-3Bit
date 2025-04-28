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
    <style>
        body {
            background-color:rgb(40, 40, 43);
            font-family: 'Segoe UI', sans-serif;
            color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .edit-user-container {
            background-color: #3c3d43;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }

        .edit-user-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #ffffff;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background-color:rgb(10, 10, 10);
            color: #fff;
        }

        input:focus, select:focus {
            outline: none;
            background-color: #505378;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            background-color:black;
            color: white;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color:darkslategray;
        }

        .btn.secondary {
            background-color: #6c757d;
        }

        .btn.secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="edit-user-container">
        <h2>Edit User</h2>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="citizen" <?= $user['role'] === 'citizen' ? 'selected' : '' ?>>Citizen</option>
                <option value="service_provider" <?= $user['role'] === 'service_provider' ? 'selected' : '' ?>>Service Provider</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <div class="form-actions">
                <button type="submit" class="btn">Update</button>
                <a href="manage_users.php" class="btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
