<?php
session_start();

// Check if the user is already logged in, if so then redirect to the home page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home.php");
    exit;
}

// Destroy the session and redirect to the login page
session_destroy();
header("Location: login.html");
exit;
?>
