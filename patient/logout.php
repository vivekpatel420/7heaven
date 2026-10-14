<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to Home page
header("Location: ../index.php");
exit();
?>