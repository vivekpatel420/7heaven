<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

if(!isset($_GET['id'])){
    header("Location: view_patients.php");
    exit();
}

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM patients WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

if(!$row){
    die("Patient not found.");
}

if(isset($_POST['update'])){

    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $blood = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $sql = "UPDATE patients SET
            fullname='$fullname',
            gender='$gender',
            dob='$dob',
            blood_group='$blood',
            phone='$phone',
            email='$email'
            WHERE id='$id'";

    if(mysqli_query($conn,$sql)){
        header("Location: view_patients.php");
        exit();
    }else{
        echo "Error : ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Patient</title>

<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/form.css">

</head>

<body>

<div class="container">

<?php include("sidebar.php"); ?>

<div class="content">

<div class="form-card">

<h1>🩺 Edit Patient</h1>

<form method="POST">

<div class="form-group">
<label>Full Name</label>
<input type="text"
name="fullname"
value="<?php echo htmlspecialchars($row['fullname']); ?>"
placeholder="Enter Full Name"
required>
</div>

<div class="form-group">
<label>Gender</label>

<select name="gender" required>

<option value="Male" <?php if($row['gender']=="Male") echo "selected"; ?>>Male</option>

<option value="Female" <?php if($row['gender']=="Female") echo "selected"; ?>>Female</option>

<option value="Other" <?php if($row['gender']=="Other") echo "selected"; ?>>Other</option>

</select>

</div>

<div class="form-group">
<label>Date of Birth</label>

<input type="date"
name="dob"
value="<?php echo $row['dob']; ?>"
required>

</div>

<div class="form-group">
<label>Blood Group</label>

<input type="text"
name="blood_group"
value="<?php echo htmlspecialchars($row['blood_group']); ?>"
placeholder="O+, A+, B+, AB+"
required>

</div>

<div class="form-group">
<label>Phone Number</label>

<input type="text"
name="phone"
value="<?php echo htmlspecialchars($row['phone']); ?>"
placeholder="9876543210"
required>

</div>

<div class="form-group">
<label>Email Address</label>

<input type="email"
name="email"
value="<?php echo htmlspecialchars($row['email']); ?>"
placeholder="patient@gmail.com"
required>

</div>

<button type="submit" class="save-btn" name="update">
💾 Update Patient
</button>

</form>

</div>

</div>

</div>

</body>

</html>