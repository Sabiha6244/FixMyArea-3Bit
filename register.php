<?php
require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Account.php");
require_once("includes/classes/Constants.php");

$account = new Account($con);
$errorMessage = ""; // Initialize error message

if (isset($_POST["submitButton"])) {

    $name = FormSanitizer::sanitizeFromString($_POST["name"]);
    $phone = FormSanitizer::sanitizeFromString($_POST["phone"]);
    $role = FormSanitizer::sanitizeFromUsername($_POST["role"]);
    $email = FormSanitizer::sanitizeFromEmail($_POST["email"]);
    $email2 = FormSanitizer::sanitizeFromEmail($_POST["email2"]);
    $password = FormSanitizer::sanitizeFromPassword($_POST["password"]);
    $password2 = FormSanitizer::sanitizeFromPassword($_POST["password2"]);

    $result = $account->register($name, $phone, $role, $email, $email2, $password, $password2);

    if ($result === true) {
        $_SESSION["userLoggedIn"] = $email;
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = "<div class='errorMessage'>$result</div>"; // Store error message
    }
}

function getInputValue($inputName) {
    return isset($_POST[$inputName]) ? htmlspecialchars($_POST[$inputName]) : "";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report, View, or Discuss Local Problems</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
</head>

<body>
    <div class="signInContainer">
        <div class="column">

            <div class="header">
                <img src="assets/images/logo.png" title="Logo" alt="Site Logo" />
                <h3>Sign Up</h3>
                <span>to Report, View, or Discuss Local Problems</span>
            </div>

            <form method="POST">
                
                <!-- Display Error Message -->
                <?php if (!empty($errorMessage)) echo $errorMessage; ?>

                <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                <input type="text" name="name" placeholder="Please Insert Your Name" value="<?php echo getInputValue("name"); ?>" required>

                <input type="tel" name="phone" placeholder="Please Insert Your Phone Number" value="<?php echo getInputValue('phone'); ?>" pattern="[0-9]{11}" required>
                <?php echo $account->getError(Constants::$invalidPhoneNumber); ?>
                <?php echo $account->getError(Constants::$phoneNumberTaken); ?>

                <select name="role" required>
                    <option value="" disabled selected>Please Select Your Role</option>
                    <option value="citizen" <?php echo getInputValue("role") == "citizen" ? "selected" : ""; ?>>Citizen</option>
                    <option value="service_provider" <?php echo getInputValue("role") == "service_provider" ? "selected" : ""; ?>>Service Provider</option>
                    <option value="admin" <?php echo getInputValue("role") == "admin" ? "selected" : ""; ?>>Admin</option>
                </select>
                <?php echo $account->getError(Constants::$invalidRole); ?>

                <?php echo $account->getError(Constants::$emailsDontMatch); ?>
                <?php echo $account->getError(Constants::$emailInvalid); ?>
                <?php echo $account->getError(Constants::$emailTaken); ?>
                <input type="email" name="email" placeholder="Please Insert Your Email" value="<?php echo getInputValue("email"); ?>" required>
                <input type="email" name="email2" placeholder="Please Confirm Your Email" value="<?php echo getInputValue("email2"); ?>" required>

                <?php echo $account->getError(Constants::$passwordsDontMatch); ?>
                <?php echo $account->getError(Constants::$passwordLength); ?>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="password2" placeholder="Confirm Password" required>

                <input type="submit" name="submitButton" value="SUBMIT">
            </form>

            <a href="login.php" class="signInMessages">Already have an account? Sign in here!</a>

        </div>
    </div>
</body>

</html>
