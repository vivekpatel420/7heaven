<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM doctors WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {

        header("Location: view_doctors.php");
        exit();

    } else {

        echo "Error deleting doctor: " . mysqli_error($conn);

    }

} else {

    header("Location: view_doctors.php");
    exit();

}
?>