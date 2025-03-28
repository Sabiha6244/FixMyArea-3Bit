<?php

require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Account.php");
require_once("includes/classes/Constants.php");

$account = new Account($con);

if (isset($_POST["submitButton"])) {

    $email = FormSanitizer::sanitizeFromUsername($_POST["email"]);  // Use $email instead of $name
    $password = FormSanitizer::sanitizeFromPassword($_POST["password"]);

    $success = $account->login($email, $password);  // Use $email

    if ($success) {
        $_SESSION["userLoggedIn"] = $email;
        header("Location: index.php");
        exit(); // Ensure the script stops execution after redirecting
    }
}

function getInputValue($name){
    if(isset($_POST[$name])){
        echo htmlspecialchars($_POST[$name]); // Prevent XSS attacks
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report, view, or discuss local problems</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
</head>

<body>
    <div class="signInContainer">
        <div class="column">

            <div class="header">
                <img src="assets/images/logo.png" title="Logo" alt="site logo" />
                <h3>Sign In</h3>
                <span>to continue FixMyArea</span>
            </div>
            <form method="POST">

                <?php echo $account->getError(Constants::$loginFailed); ?>

                <input type="email" name="email" placeholder="Email" value="<?php getInputValue("email"); ?>" required>

                <input type="password" name="password" placeholder="Password" required>

                <input type="submit" name="submitButton" value="SUBMIT"> <!-- Fixed 'values' to 'value' -->

            </form>
            <a href="register.php" class="signInMessages">Need an Account? Sign Up here!</a>

        </div>
    </div>
</body>

</html>
