<?php
// config.php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Europe/London");

try {
    $con = new PDO("mysql:dbname=fixmyarea;host=localhost", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage());
}
?>
