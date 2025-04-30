<?php
// manage_providers.php
session_start();
require_once("../includes/config.php");

// Check admin access
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

// Handle actions
if (isset($_GET['action'], $_GET['id'])) {
    $providerId = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'verify') {
        $con->prepare("UPDATE service_providers SET verified = 1 WHERE id = ?")->execute([$providerId]);
    } elseif ($action === 'unverify') {
        $con->prepare("UPDATE service_providers SET verified = 0 WHERE id = ?")->execute([$providerId]);
    } elseif ($action === 'delete') {
        $con->prepare("DELETE FROM service_providers WHERE id = ?")->execute([$providerId]);
    }

    header("Location: manage_providers.php");
    exit();
}

// Fetch providers with user email and phone
$stmt = $con->query("
    SELECT sp.*, u.email, u.phone 
    FROM service_providers sp
    JOIN users u ON sp.user_id = u.id
    ORDER BY sp.user_id DESC
");

if (!$stmt) {
    die("Query failed: " . implode(" ", $con->errorInfo()));
}

$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Providers - Admin Dashboard | FixMyArea</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background-color: #555;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar .logo {
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }

        .sidebar img {
            width: 100px;
            margin: 0 auto 1rem;
        }

        .sidebar nav a {
            display: block;
            color: #ccc;
            padding: 0.8rem;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: background 0.3s;
        }

        .sidebar nav a.active,
        .sidebar nav a:hover {
            background-color: #444;
            color: #fff;
        }

        .sidebar .bottom-text {
            font-size: 0.8rem;
            text-align: center;
            color: #888;
        }

        .main-content {
            margin-left: 220px;
            padding: 2rem;
            width: 100%;
            background-color: 0 2px 5px rgba(0, 0, 0, 0.1);
            min-height: 100vh;
        }

        h2 {
            margin-bottom: 1.5rem;
            color: whitesmoke;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: darkgrey;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #eee;

        }

        th {
            background-color: dimgray;
            color: whitesmoke;
        }

        td {
            color: black;
        }

        .actions a {
            display: inline-block;
            padding: 6px 12px;
            font-size: 14px;
            color: #fff;
            background-color: rgb(54, 60, 67);
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
            transition: 0.2s;
        }

        .actions a.delete {
            background-color: rgb(28, 26, 27);
        }

        .actions a:hover {
            opacity: 0.9;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #888;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="logo">FixMyArea</div>
            <img src="../assets/images/logo.png" alt="Logo">
            <nav>
                <a href="admin.php">Dashboard</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="manage_issues.php">Manage Issues</a>
                <a href="manage_providers.php" class="active">Manage Providers</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
        <div class="bottom-text">© <?= date("Y") ?> FixMyArea</div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Manage Service Providers</h2>

        <?php if ($providers): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service Type</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($providers as $provider): ?>
                        <tr>
                            <td><?= $provider['user_id'] ?></td>
                            <td><?= htmlspecialchars($provider['company_name']) ?></td>
                            <td><?= htmlspecialchars($provider['email']) ?></td>
                            <td><?= htmlspecialchars($provider['phone']) ?></td>
                            <td><?= htmlspecialchars($provider['service_type']) ?></td>
                            <td><?= $provider['verified'] ? 'Yes' : 'No' ?></td>
                            <td class="actions">
                                <?php if ($provider['verified']): ?>

                                    <a href="verify_provider.php?action=unverify&id=<?= $provider['user_id'] ?>">Unverify</a>
                                    <?php else: ?>
                                    <a href="verify_provider.php?id=<?= $provider['user_id'] ?>" class="verify" style="background-color: green;">Verify</a>
                                    <?php endif; ?>

                                    <a href="?action=delete&id=<?= $provider['user_id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this provider?')">Delete</a>
                                    </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">No service providers found.</div>
        <?php endif; ?>
    </div>

</body>

</html>