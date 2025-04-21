<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch issues with user info
$sql = "SELECT issues.*, users.name AS reporter_name
        FROM issues
        JOIN users ON issues.citizen_id = users.id
        ORDER BY issues.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Issues - FixMyArea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2e2e2e;
            color: white;
        }


        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #555;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
        }

        .sidebar .logo {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar img {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 100%;
            height: auto;
            max-height: 100px;
            border-radius: 10px;
        }


        .sidebar nav a {
            display: block;
            padding: 12px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar nav a:hover {
            background-color: #34495e;
        }

        .sidebar .bottom-text {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 12px;
            color: #bdc3c7;
        }

        .main {
            margin-left: 270px;
            padding: 20px;
        }

        .card {
            background-color: #444;
            color: whitesmoke;
            border: none;
        }

        .card-title {
            font-size: 1.2rem;
        }

        .btn-custom {
            background-color:rgb(40, 40, 41);
            border: none;
            color: white;
        }

        .btn-custom:hover {
            background-color:rgb(68, 70, 73);
        }

        .action-btns a {
            margin-right: 10px;
            background-color:rgb(68, 70, 73);
            
        
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="../assets/images/logo.png" title="Logo" alt="FixMyArea Logo" />
    
    <nav>
        <a href="../index.php">Home</a>
        <a href="../report.php">Report an Issue</a>
        <a href="../track.php">Track Issues</a>
        <a href="admin.php">Admin Dashboard</a>
        <a href="../logout.php">Logout</a>
        <p style="margin-top: 100px; font-size: 0.9rem;">© 2025 FixMyArea</p>
    </nav>
    </div>


    <div class="main">
        <h2>Manage Reported Issues</h2>
        <hr>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card p-3">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p><strong>Reported By:</strong> <?php echo htmlspecialchars($row['reporter_name']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                            <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['created_at'])); ?></p>
                            <p><strong>Description:</strong><br>
                                <?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 100))) . '...'; ?>
                            </p>
                            <?php if (!empty($row['photo_path'])): ?>
                                <img src="/3Bit/<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Issue Image" class="img-fluid rounded mb-2" style="max-height: 200px;">
                            <?php endif; ?>
                            <div class="action-btns">
                                <a href="view_report.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View Report</a>
                                <a href="assign_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Assign Task</a>
                                <a href="track_payment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Track Payment</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No issues reported yet.</p>
        <?php endif; ?>

    </div>

</body>

</html>

<?php $conn->close(); ?>