<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

if (!isset($_GET['id'])) {
    header("Location: view_doctors.php");
    exit();
}

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM doctors WHERE id='$id'");
$doctor = mysqli_fetch_assoc($result);

if (!$doctor) {
    die("Doctor not found.");
}

if (isset($_POST['update'])) {

    $fullname = $_POST['fullname'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $experience = $_POST['experience'];

    $sql = "UPDATE doctors SET
            fullname='$fullname',
            specialization='$specialization',
            phone='$phone',
            email='$email',
            experience='$experience'
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: view_doctors.php");
        exit();
    } else {
        echo "Error : " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Doctor</title>

<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/form.css">

</head>

<body>

<div class="container">

<?php include("sidebar.php"); ?>

<div class="content">

<div class="form-card">

<h1>👨‍⚕️ Edit Doctor</h1>

<form method="POST">

<div class="form-group">
<label>Doctor Name</label>
<input type="text"
name="fullname"
value="<?php echo htmlspecialchars($doctor['fullname']); ?>"
placeholder="Enter Doctor Name"
required>
</div>

<div class="form-group">
<label>Specialization</label>
<input type="text"
name="specialization"
value="<?php echo htmlspecialchars($doctor['specialization']); ?>"
placeholder="Cardiologist"
required>
</div>

<div class="form-group">
<label>Phone Number</label>
<input type="text"
name="phone"
value="<?php echo htmlspecialchars($doctor['phone']); ?>"
placeholder="9876543210"
required>
</div>

<div class="form-group">
<label>Email Address</label>
<input type="email"
name="email"
value="<?php echo htmlspecialchars($doctor['email']); ?>"
placeholder="doctor@gmail.com"
required>
</div>

<div class="form-group">
<label>Experience (Years)</label>
<input type="number"
name="experience"
value="<?php echo htmlspecialchars($doctor['experience']); ?>"
placeholder="5"
required>
</div>

<button type="submit" class="save-btn" name="update">
💾 Update Doctor
</button>

</form>

</div>

</div>

</div>

</body>
</html>