<?php
session_start();
require_once("includes/config.php");

$userEmail = $_SESSION["userLoggedIn"] ?? null;

if (!$userEmail) {
    header("Location: login.php");
    exit();
}

// Get user ID
$stmt = $con->prepare("SELECT id, role FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData || $userData['role'] !== 'service_provider') {
    header("Location: index.php");
    exit();
}

$user_id = $userData['id'];
$error = "";
$success = "";

// Check if provider profile already exists
$stmt = $con->prepare("SELECT * FROM service_providers WHERE user_id = ?");
$stmt->execute([$user_id]);
if ($stmt->rowCount() > 0) {
    header("Location: dashboard/provider.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $service_type = trim($_POST['service_type']);
    $description = trim($_POST['description']);
    $service_area = trim($_POST['service_area']);

    if (empty($company_name) || empty($service_type) || empty($service_area)) {
        $error = "Please fill in all required fields.";
    } else {
        $insert = $con->prepare("INSERT INTO service_providers (user_id, company_name, service_type, description, service_area, verified) VALUES (?, ?, ?, ?, ?, 0)");
        $insert->execute([$user_id, $company_name, $service_type, $description, $service_area]);
        $success = "Provider profile submitted successfully!";
        header("Location: dashboard/provider.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register as Service Provider</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #1e1e2f;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 650px;
            margin: 50px auto;
            background: #2b2b3d;
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            color: whitesmoke;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
            background: #3b3b50;
            color: #fff;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .btn {
            background-color: #4c4cff;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            display: inline-block;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            margin-top: 15px;
            color: #ff6b6b;
        }

        .success {
            color: #00ff99;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Register as a Service Provider</h2>

        <?php if ($error): ?>
            <p class="message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="message success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="company_name">Company Name *</label>
            <input type="text" name="company_name" id="company_name" required>

            <label for="service_type">Service Type *</label>
            <select name="service_type" id="service_type" required>
                <option value="">Select Service Type</option>
                <option value="Road Repair">Road Repair: Road Repair, Potholes, Sidewalk Issues</option>
                <option value="Sanitation">Sanitation: Garbage Collection, Public Cleanliness</option>
                <option value="Utilities">Utilities: Water, Electricity, Gas Issues</option>
                <option value="Public Safety">Public Safety: Street Lights, Traffic Signals, Safety Hazards</option>
                
            </select>

            <label for="description">Service Description</label>
            <textarea name="description" id="description" placeholder="Describe your services..."></textarea>

            <label for="service_area">Service Area *</label>
            <input type="text" name="service_area" id="service_area" required>

            <button type="submit" class="btn">Submit Profile</button>
        </form>
    </div>
</body>

</html>