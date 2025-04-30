<?php
session_start();


require_once("includes/config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("includes/PHPMailer/src/PHPMailer.php");
require_once("includes/PHPMailer/src/SMTP.php");
require_once("includes/PHPMailer/src/Exception.php");

$error = "";
$success = "";

// Set timezone to ensure time consistency
date_default_timezone_set('Asia/Dhaka');

// Redirect if no email is set in session
if (!isset($_SESSION["pendingVerificationEmail"])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION["pendingVerificationEmail"];

// Generate a random 6-digit OTP
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Send OTP to email using PHPMailer
function sendOTP($con, $email) {
    $otp = generateOTP();
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Update OTP in the database
    $stmt = $con->prepare("UPDATE users SET otp_code = :otp, otp_expires_at = :expiry WHERE email = :email");
    $stmt->bindParam(":otp", $otp);
    $stmt->bindParam(":expiry", $expiry);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sabiha.akter.6244@gmail.com';
        $mail->Password = 'fhjleeknyxuqymqc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sabiha.akter.6244@gmail.com', 'FixMyArea');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "<p>Your FixMyArea OTP is: <strong>$otp</strong>. It will expire in 10 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Failed to send OTP. Mailer Error: " . $mail->ErrorInfo;
    }
}

// Handle OTP submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["otp"])) {
    $enteredOtp = trim($_POST["otp"]);

    $stmt = $con->prepare("SELECT otp_code, otp_expires_at FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $dbOtp = $result['otp_code'];
        $otpExpiresAt = $result['otp_expires_at'];
        $currentTime = date("Y-m-d H:i:s");

        if ($enteredOtp === $dbOtp && strtotime($currentTime) <= strtotime($otpExpiresAt)) {
            $update = $con->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE email = :email");
            $update->bindParam(":email", $email);
            $update->execute();

            unset($_SESSION["pendingVerificationEmail"]);
            $_SESSION["userLoggedIn"] = $email;

            $success = "Email verified successfully. Redirecting...";
            header("Location: profile_setup.php");
            exit();
        } else {
            $error = "Invalid or expired OTP.";
        }
    } else {
        $error = "Something went wrong. Please try registering again.";
    }
}

// Handle Resend OTP
if (isset($_GET['resend']) && $_GET['resend'] === 'true') {
    $sendResult = sendOTP($con, $email);
    if ($sendResult === true) {
        $success = "A new OTP has been sent to your email.";
    } else {
        $error = $sendResult;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verify Email - FixMyArea</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
</head>

<body>
    <div class="signInContainer">
        <div class="column">
            <div class="header">
                <img src="assets/images/logo.png" title="Logo" alt="Site Logo" />
                <h3>Verify Your Email</h3>
                <span>An OTP has been sent to <strong><?php echo htmlspecialchars($email); ?></strong>. Please enter it below:</span>
            </div>

            <?php if ($error): ?>
                <div class="errorMessage"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="successMessage"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="otp" placeholder="Enter 6-digit OTP" pattern="\d{6}" maxlength="6" required>
                <input type="submit" value="Verify">
            </form>

            <a href="verify_email.php?resend=true" class="signInMessages">Resend OTP</a><br>
            <a href="register.php" class="signInMessages">Back to Register</a>
        </div>
    </div>
</body>

</html>
