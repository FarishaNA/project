<?php
session_start(); // Start the session if it hasn't been started already

// Unset all session variables
$_SESSION = array();  // This clears the session variables

// Destroy the session
session_destroy();    // This completely ends the session

// Redirect to the login page
header("Location: login.php");
exit();
?>
