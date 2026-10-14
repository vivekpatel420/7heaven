<?php
session_start();

// Clear session data
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the Home page
header("Location: ../index.php");
exit();
?>