<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$issueId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch issue details
$issueQuery = $conn->prepare("SELECT * FROM issues WHERE id = ?");
$issueQuery->bind_param("i", $issueId);
$issueQuery->execute();
$issueResult = $issueQuery->get_result();
$issue = $issueResult->fetch_assoc();

if (!$issue) {
    die("Issue not found.");
}

// Fetch all service providers
$providers = $conn->query("SELECT * FROM service_providers");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $providerId = (int)$_POST['provider_id'];
    $citizenId = $issue['citizen_id']; // From the issue table

    // Insert into service_requests
    $stmt = $conn->prepare("INSERT INTO service_requests (citizen_id, provider_id, issue_id, status, payment_status) VALUES (?, ?, ?, 'in_progress', 'pending')");
    $stmt->bind_param("iii", $citizenId, $providerId, $issueId);
    $stmt->execute();

    // Update issue status
    $conn->query("UPDATE issues SET status = 'In Progress' WHERE id = $issueId");

    echo "<script>alert('Task assigned successfully!'); window.location.href='admin.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assign Task - FixMyArea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #2e2e2e;
            color: white;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            background-color: #444;
            color: white;
            padding: 20px;
        }

        .btn-custom {
            background-color: #28a745;
            border: none;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        select.form-control {
            background-color: #2e2e2e;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Assign Task</h2>
        <hr>
        <div class="card p-4">
            <h4>Issue Title: <?php echo htmlspecialchars($issue['title']); ?></h4>
            <p><?php echo htmlspecialchars($issue['description']); ?></p>

            <form method="POST">
                <div class="form-group">
                    <label for="provider_id">Select Service Provider:</label>
                    <select class="form-control" name="provider_id" required>
                        <option value="">-- Choose Provider --</option>
                        <?php while ($row = $providers->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['user_id']); ?>">
                                <?php echo htmlspecialchars($row['company_name'] . ' - ' . $row['service_type']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <br>
                <button type="submit" class="btn btn-success">Assign Task</button>
            </form>
        </div>
    </div>

</body>

</html>