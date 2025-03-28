<?php

require_once("includes/config.php");
require_once("includes/classes/PreviewProvider.php");




if (!isset($_SESSION["userLoggedIn"])) {
    header("Location: register.php");
}
$userLoggedIn = $_SESSION["userLoggedIn"];
?>

<!DOCTYPE html>

<head>
    <title>Welcome to FixMyArea</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/667c17f151.js" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</head>
<body>
    <div class='wrapper'>