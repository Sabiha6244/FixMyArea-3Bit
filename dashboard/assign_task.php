<?php
session_start();
$adminId = $_SESSION['user_id'] ?? 0;

$conn = new mysqli("localhost", "root", "", "3bit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$issueId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch issue and citizen address
$issueQuery = $conn->prepare("
    SELECT i.*, u.division, u.district, u.city_corporation, u.upazila
    FROM issues i
    JOIN users u ON i.citizen_id = u.id
    WHERE i.id = ?
");
$issueQuery->bind_param("i", $issueId);
$issueQuery->execute();
$issueResult = $issueQuery->get_result();
$issue = $issueResult->fetch_assoc();

if (!$issue) {
    die("Issue not found.");
}

$category = $issue['category'];
$division = $issue['division'];
$district = $issue['district'];
$cityCorp = $issue['city_corporation'];
$upazila = $issue['upazila'];

// Match providers
$providerQuery = $conn->prepare("
    SELECT sp.*, u.email, u.phone, u.id as user_id, u.division, u.district, u.city_corporation
    FROM service_providers sp
    JOIN users u ON sp.user_id = u.id
    WHERE sp.service_type = ?
    AND u.division = ?
    AND u.district = ?
    AND u.city_corporation = ?
");
$providerQuery->bind_param("ssss", $category, $division, $district, $cityCorp);
$providerQuery->execute();
$providers = $providerQuery->get_result();

// Feedback if no match
$checkServiceType = $conn->prepare("SELECT * FROM service_providers WHERE service_type = ?");
$checkServiceType->bind_param("s", $category);
$checkServiceType->execute();
$serviceTypeMatch = $checkServiceType->get_result()->num_rows;

$checkLocation = $conn->prepare("
    SELECT * FROM users
    WHERE division = ? AND district = ? AND city_corporation = ?
");
$checkLocation->bind_param("sss", $division, $district, $cityCorp);
$checkLocation->execute();
$locationMatch = $checkLocation->get_result()->num_rows;

$noMatchReason = "";
if ($providers->num_rows === 0) {
    if (!$serviceTypeMatch && !$locationMatch) {
        $noMatchReason = "❌ No service providers found with matching service type <b>and</b> location.";
    } elseif (!$serviceTypeMatch) {
        $noMatchReason = "❌ No service providers found for the issue category: <strong>$category</strong>.";
    } elseif (!$locationMatch) {
        $noMatchReason = "❌ No providers found in: <strong>$division, $district, $cityCorp</strong>.";
    } else {
        $noMatchReason = "❌ No providers match both the service type and location.";
    }
}

$assignedProviderName = "";
$assignedProviderId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $providerId = (int)$_POST['provider_id'];
    $citizenId = $issue['citizen_id'];

    // Insert into service_requests
    $stmt = $conn->prepare("INSERT INTO service_requests (citizen_id, provider_id, issue_id, status, payment_status, admin_id) VALUES (?, ?, ?, 'in_progress', 'pending', ?)");
    $stmt->bind_param("iiii", $citizenId, $providerId, $issueId, $adminId);
    $stmt->execute();

    // Update issue
    $conn->query("UPDATE issues SET status = 'in_progress', hired_service_provider_id = $providerId WHERE id = $issueId");

    // Get provider name for messaging
    $providerResult = $conn->query("SELECT company_name FROM service_providers WHERE user_id = $providerId");
    $providerRow = $providerResult->fetch_assoc();
    $assignedProviderName = $providerRow['company_name'];
    $assignedProviderId = $providerId;

    // Flag that assignment happened
    $taskAssigned = true;
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

        select.form-control,
        input.form-control {
            background-color: #2e2e2e;
            color: white;
        }

        .alert-custom {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .success-box {
            background-color: #28a745;
            color: white;
            padding: 12px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Assign Task</h2>
    <hr>
    <div class="card p-4">
        <h4>Issue Title: <?= htmlspecialchars($issue['title']); ?></h4>
        <p><strong>Description:</strong> <?= htmlspecialchars($issue['description']); ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($issue['category']); ?></p>
        <p><strong>Reporter Address:</strong> <?= "{$division}, {$district}, {$cityCorp}, {$upazila}" ?></p>

        <?php if (!empty($noMatchReason)): ?>
            <div class="alert-custom mb-3"><?= $noMatchReason ?></div>
        <?php endif; ?>

        <?php if (isset($taskAssigned) && $taskAssigned): ?>
            <div class="success-box">
                ✅ Task assigned successfully to <strong><?= htmlspecialchars($assignedProviderName) ?></strong>!
            </div>
            <br>
            <button class="btn btn-primary" onclick="messageProvider(<?= $adminId ?>, <?= $assignedProviderId ?>, <?= $issueId ?>)">
                Message <?= htmlspecialchars($assignedProviderName) ?>
            </button>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="provider_id">Matching Service Providers:</label>
                    <select class="form-control" name="provider_id" id="provider_id" required>
                        <option value="">Choose Provider</option>
                        <?php $providers->data_seek(0); ?>
                        <?php while ($row = $providers->fetch_assoc()): ?>
                            <option value="<?= $row['user_id'] ?>">
                                <?= htmlspecialchars($row['company_name']) . " ({$row['service_type']}) - {$row['district']}" ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom">Assign Task</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function messageProvider(adminId, providerId, issueId) {
        window.location.href = `chat.php?sender_id=${adminId}&receiver_id=${providerId}&issue_id=${issueId}`;
    }
</script>
</body>
</html>
