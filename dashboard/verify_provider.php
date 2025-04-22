<?php
// verify_provider.php
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

$providerId = $_GET['id'] ?? null;

if (!$providerId) {
    echo "Invalid request.";
    exit();
}

// Fetch provider and user details
$stmt = $con->prepare("
    SELECT u.*, sp.company_name, sp.service_type, sp.description, sp.service_area, sp.rating, sp.verified
    FROM users u
    JOIN service_providers sp ON u.id = sp.user_id
    WHERE u.id = ?
");
$stmt->execute([$providerId]);
$provider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider) {
    echo "Provider not found.";
    exit();
}

// Handle verification or decline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify'])) {
        $verifyStmt = $con->prepare("UPDATE service_providers SET verified = 1 WHERE user_id = ?");
        $verifyStmt->execute([$providerId]);
        header("Location: manage_providers.php");
        exit();
    }

    if (isset($_POST['decline']) && !$provider['is_verified']) {
        $delProvider = $con->prepare("DELETE FROM service_providers WHERE user_id = ?");
        $delProvider->execute([$providerId]);

        $delUser = $con->prepare("DELETE FROM users WHERE id = ?");
        $delUser->execute([$providerId]);

        header("Location: manage_providers.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Provider | FixMyArea</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a1a;
            color: #f0f0f0;
            margin: 0;
            padding: 2rem;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #333;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .info-group span {
            display: block;
            background: #444;
            padding: 10px;
            border-radius: 5px;
        }

        .profile-pic {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-pic img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #666;
        }

        .actions {
            text-align: center;
            margin-top: 2rem;
        }

        .actions form {
            display: inline;
        }

        .actions button {
            padding: 10px 20px;
            background: darkslategray;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            margin: 0 8px;
        }

        .actions button:hover {
            background: black;
        }

        .actions .decline-btn {
            background: crimson;
        }

        .actions .decline-btn:hover {
            background: darkred;
        }

        .actions button[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: whitesmoke;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>

    <script>
        function confirmDecline() {
            return confirm("Are you sure you want to decline this provider? This will permanently delete their data.");
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Verify Service Provider</h2>

        <?php
        $profilePic = $provider['profile_picture'];
        $imagePath = (!empty($profilePic) && file_exists("../" . $profilePic))
            ? "../" . $profilePic
            : "../assets/images/default-avatar.png";
        ?>

        <div class="profile-pic">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Picture">
        </div>

        <div class="info-group">
            <label>Name</label>
            <span><?= htmlspecialchars($provider['name']) ?></span>
        </div>

        <div class="info-group">
            <label>Email</label>
            <span><?= htmlspecialchars($provider['email']) ?></span>
        </div>

        <div class="info-group">
            <label>Phone</label>
            <span><?= htmlspecialchars($provider['phone']) ?></span>
        </div>

        <div class="info-group">
            <label>Email Verified</label>
            <span><?= $provider['is_verified'] ? 'Yes' : 'No' ?></span>
        </div>

        <div class="info-group">
            <label>Address</label>
            <span>
                <?=
                "Div: " . ($provider['division'] ?? 'N/A') .
                    ", Dist: " . ($provider['district'] ?? 'N/A') .
                    ", City: " . ($provider['city_corporation'] ?? 'N/A') .
                    ", Upazila: " . ($provider['upazila'] ?? 'N/A') .
                    ", Postcode: " . ($provider['postcode'] ?? 'N/A');
                ?>
            </span>
        </div>

        <div class="info-group">
            <label>Company Name</label>
            <span><?= htmlspecialchars($provider['company_name']) ?></span>
        </div>

        <div class="info-group">
            <label>Service Type</label>
            <span><?= htmlspecialchars($provider['service_type']) ?></span>
        </div>

        <div class="info-group">
            <label>Description</label>
            <span><?= nl2br(htmlspecialchars($provider['description'])) ?></span>
        </div>

        <div class="info-group">
            <label>Service Area</label>
            <span><?= htmlspecialchars($provider['service_area']) ?></span>
        </div>

        <div class="actions">
            <form method="POST">
                <button type="submit" name="verify">Confirm Verification</button>
            </form>

            <form method="POST" onsubmit="return confirmDecline();">
                <button type="submit" name="decline" class="decline-btn" <?= $provider['is_verified'] ? 'disabled' : '' ?>>Decline Provider</button>
            </form>

            <div style="margin-top: 1rem;">
                <a class="back-link" href="manage_providers.php">← Back to Manage Providers</a>
            </div>
        </div>
    </div>
</body>

</html>