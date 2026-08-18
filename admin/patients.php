<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$message = "";

if(isset($_POST['save'])){

    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $blood = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $emergency = $_POST['emergency_contact'];

    $sql = "INSERT INTO patients(fullname, gender, dob, blood_group, phone, email, address, emergency_contact)
            VALUES('$fullname','$gender','$dob','$blood','$phone','$email','$address','$emergency')";

    if(mysqli_query($conn,$sql)){
        $message = "✅ Patient Added Successfully!";
    }else{
        $message = "❌ Error : ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Patient</title>

    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/form.css">

</head>

<body>

<div class="container">

    <?php include("sidebar.php"); ?>

    <div class="content">

        <div class="form-card">

            <h1>🧑 Add Patient</h1>

            <?php
            if($message!=""){
                echo "<div class='success'>$message</div>";
            }
            ?>

            <form method="POST">

                <div class="form-group">
                    <label>Full Name</label>
                    <input
                        type="text"
                        name="fullname"
                        placeholder="Enter Full Name"
                        required>
                </div>

                <div class="form-group">
                    <label>Gender</label>

                    <select name="gender" required>

                        <option value="">Select Gender</option>

                        <option value="Male">Male</option>

                        <option value="Female">Female</option>

                        <option value="Other">Other</option>

                    </select>

                </div>

                <div class="form-group">
                    <label>Date of Birth</label>

                    <input
                        type="date"
                        name="dob"
                        required>

                </div>

                <div class="form-group">
                    <label>Blood Group</label>

                    <select name="blood_group" required>

                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>

                    </select>

                </div>

                <div class="form-group">
                    <label>Phone Number</label>

                    <input
                        type="text"
                        name="phone"
                        placeholder="9876543210"
                        required>

                </div>

                <div class="form-group">
                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="patient@gmail.com"
                        required>

                </div>

                <div class="form-group">
                    <label>Address</label>

                    <textarea
                        name="address"
                        placeholder="Enter Full Address"
                        required></textarea>

                </div>

                <div class="form-group">
                    <label>Emergency Contact</label>

                    <input
                        type="text"
                        name="emergency_contact"
                        placeholder="Emergency Contact Number"
                        required>

                </div>

                <button class="save-btn" type="submit" name="save">
                    ➕ Add Patient
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>