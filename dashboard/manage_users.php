<?php
// admin/manage_users.php

session_start();
require_once("../includes/config.php");

// Ensure only admin can access this page
$userEmail = $_SESSION["userLoggedIn"] ?? null;
$userRole = null;

if ($userEmail) {
    $query = $con->prepare("SELECT role FROM users WHERE email = ?");
    $query->execute([$userEmail]);
    $userRole = $query->fetchColumn();
}

if ($userRole !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Search logic
$search = $_GET['search'] ?? "";
$usersQuery = $con->prepare("SELECT id, name, email, role FROM users WHERE name LIKE ? OR email LIKE ?");
$usersQuery->execute(["%$search%", "%$search%"]);
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin | FixMyArea</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        .manage-users {
            padding: 1rem;
        }

        .manage-users h2 {
            margin-bottom: 3rem;
            color: whitesmoke;
            font-size: 2.5rem;
        }

        .search-box {
            margin-bottom: 3rem;
            
        }

        .search-box input[type="text"] {
            padding: 0.8rem;
            width: 300px;
            margin-right: 1rem;
            border-radius: 4.5px;
            border: 1px solid #ccc;
        }

        .search-box button {
            padding: 0.8rem 1.2rem;
            background-color:rgb(83, 87, 90);
            border: none;
            color: whitesmoke;
            font-size: 1.2rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            border: 1px solid #ccc;
            padding: 0.8rem;
            text-align: center;
            color: whitesmoke;
        }

        .users-table th {
            background-color:darkslategray;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: darkgray;
            color: white;
        }

        .delete-btn {
            background-color:darkslategrey;
            color: white;
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            text-decoration: none;
            color:whitesmoke;
            font-size: 1.8rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="../assets/images/logo.png" alt="Logo" />
        <nav>
            <a href="../index.php">Home</a>
            <a href="../report.php">Report an Issue</a>
            <a href="../issues.php">Track Issues</a>
            <a href="admin.php">Admin Dashboard</a>
            <a href="../logout.php">Logout</a>
        </nav>
        <div class="bottom-text">© <?= date("Y") ?> FixMyArea</div>
    </div>

    <div class="main-content">
        <div class="manage-users">
            <h2>Manage Users</h2>

            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Search</button>
            </form>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a class="back-link" href="admin.php">&larr; Back to Dashboard</a>
        </div>
    </div>
</body>

</html>
