<?php
// admin/delete_user.php

session_start();
require_once("../includes/config.php");

// Check if user is logged in
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

// Prevent deleting own account
$query = $con->prepare("SELECT id FROM users WHERE email = ?");
$query->execute([$userEmail]);
$adminId = $query->fetchColumn();

if ($adminId == $userId) {
    header("Location: manage_users.php?error=You cannot delete your own account.");
    exit;
}

// Delete the user
$deleteQuery = $con->prepare("DELETE FROM users WHERE id = ?");
$deleteQuery->execute([$userId]);

header("Location: manage_users.php?success=User deleted successfully.");
exit;
