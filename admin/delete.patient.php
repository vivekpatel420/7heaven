<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM patients WHERE id='$id'");

header("Location:view_patients.php");
exit();
?>