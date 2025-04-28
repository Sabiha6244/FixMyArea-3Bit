<?php
session_start();
require_once("includes/config.php");

if (!isset($_SESSION['userLoggedIn'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['userLoggedIn'];

// Fetch user info
$stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

$addressParts = array_filter([
    'Div: ' . $user['division'],
    'Dist: ' . $user['district'],
    'City-Corp: ' . $user['city_corporation'],
    'Upazila: ' . $user['upazila'],
    'Post-Code: ' . $user['postcode']
]);

$fullAddress = implode(', ', $addressParts);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile | FixMyArea</title>
    <link rel="stylesheet" href="assets/style/style.css"> <!-- Your global styles -->
    <style>
        .main-container {
            margin-left: 220px;
            padding: 30px;
            background-color: rgb(30, 32, 34);
            min-height: 100vh;
        }

        .profile-card {
            background: #555;
            border-radius: 12px;
            padding: 30px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 3px 10px rgba(45, 44, 94, 0.05);

        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 24px;
            color: whitesmoke;
        }

        .profile-header span {
            display: inline-block;
            margin-top: 5px;
            background-color: whitesmoke;
            color: rgb(42, 42, 43);
            font-size: 14px;
            padding: 2px 10px;
            border-radius: 15px;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 150px 1fr;
            row-gap: 15px;
            font-size: 16px;
            color: whitesmoke;
        }

        .profile-info .label {
            font-weight: bold;
            color: whitesmoke;
        }

        .edit-btn {
            margin-top: 30px;
            text-align: center;
        }

        .edit-btn a {
            background-color: rgb(36, 37, 39);
            color: white;
            padding: 10px 25px;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }

        .edit-btn a:hover {
            background-color: rgb(22, 23, 23);
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 20px;
            }

            .profile-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .profile-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">FixMyArea</div>
        <img src="assets/images/logo.png" alt="Logo" />
        <nav>
            <a href="index.php">Home</a>
            <a href="report.php">Report an Issue</a>
            
            <a href="logout.php">Logout</a>
        </nav>
        <div class="bottom-text">© <?= date("Y") ?> FixMyArea</div>
    </div>

    <div class="main-container">
        <div class="profile-card">
            <div class="profile-header">
                <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'assets/images/default-profile.png'; ?>" alt="Profile Picture">
                <div>
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <span><?php echo ucfirst($user['role']); ?></span>
                </div>
            </div>

            <div class="profile-info">
                <div class="label">Email:</div>
                <div><?php echo htmlspecialchars($user['email']); ?></div>

                <div class="label">Phone:</div>
                <div><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?></div>


                <div class="label">Address:</div>
                <div><?php echo !empty($fullAddress) ? htmlspecialchars($fullAddress) : 'Not provided'; ?></div>

                <div class="label">Status:</div>
                <div><?php echo ucfirst($user['status']); ?></div>

                <div class="label">Joined:</div>
                <div><?php echo date("F j, Y", strtotime($user['created_at'])); ?></div>
            </div>

            <div class="edit-btn">
                <a href="profile_setup.php">Edit Profile</a>
            </div>
        </div>
    </div>

</body>

</html>