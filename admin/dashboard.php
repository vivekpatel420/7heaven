<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

include("../config.php");

$doctorResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctors");
if(!$doctorResult){
    die("Doctor Query Error: " . mysqli_error($conn));
}
$doctor = mysqli_fetch_assoc($doctorResult);

$patientResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients");
if(!$patientResult){
    die("Patient Query Error: " . mysqli_error($conn));
}
$patient = mysqli_fetch_assoc($patientResult);

$appointmentResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments");
if(!$appointmentResult){
    die("Appointment Query Error: " . mysqli_error($conn));
}
$appointment = mysqli_fetch_assoc($appointmentResult);

$billing = 0;
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

<div class="container">

<?php include("sidebar.php"); ?>

<div class="content">

<div class="dashboard-header">

<div>

<h1>👋 Welcome Administrator</h1>

<p>7 Heaven Hospital Management System Dashboard</p>

</div>

<div class="date">
<?php echo date("l, d M Y"); ?>
</div>

</div>

<div class="dashboard-cards">

<div class="dashboard-card">

<div class="icon">👨‍⚕️</div>

<h3>Total Doctors</h3>

<h2><?php echo $doctor['total']; ?></h2>

</div>

<div class="dashboard-card">

<div class="icon">🧑</div>

<h3>Total Patients</h3>

<h2><?php echo $patient['total']; ?></h2>

</div>

<div class="dashboard-card">

<div class="icon">📅</div>

<h3>Appointments</h3>

<h2><?php echo $appointment['total']; ?></h2>

</div>

<div class="dashboard-card">

<div class="icon">💰</div>

<h3>Billing</h3>

<h2>$<?php echo $billing; ?></h2>

</div>

</div>

</div>

</div>

</body>
</html>