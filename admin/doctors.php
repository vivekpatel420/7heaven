<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config.php");

$message = "";
$message_type = "";


/* ===============================
   ADD DOCTOR
================================ */

if (isset($_POST['save'])) {

    $fullname = mysqli_real_escape_string(
        $conn,
        $_POST['fullname']
    );

    $specialization = mysqli_real_escape_string(
        $conn,
        $_POST['specialization']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $experience = intval(
        $_POST['experience']
    );


    /* CHECK EMAIL */

    $check = mysqli_query(
        $conn,
        "SELECT id FROM doctors WHERE email='$email'"
    );


    if (mysqli_num_rows($check) > 0) {

        $message = "Doctor with this email already exists.";
        $message_type = "error";

    } else {

        $sql = "
            INSERT INTO doctors
            (
                fullname,
                specialization,
                phone,
                email,
                experience
            )

            VALUES
            (
                '$fullname',
                '$specialization',
                '$phone',
                '$email',
                '$experience'
            )
        ";


        if (mysqli_query($conn, $sql)) {

            $message = "Doctor added successfully.";
            $message_type = "success";

        } else {

            $message =
                "Error: " .
                mysqli_error($conn);

            $message_type = "error";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Doctor</title>

<link rel="stylesheet"
      href="../css/dashboard.css">


<style>

/* ==================================
   DOCTOR PAGE
================================== */

.doctor-page {

    width: 100%;
    max-width: 1000px;
}


/* HEADER */

.page-header {

    margin-bottom: 25px;
}

.page-header h1 {

    color: #0b6fa4;

    margin-bottom: 7px;
}

.page-header p {

    color: #666;
}


/* ==================================
   FORM CARD
================================== */

.form-card {

    background: white;

    padding: 30px;

    border-radius: 16px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);
}


/* FORM GRID */

.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;
}


/* FORM GROUP */

.form-group {

    display: flex;

    flex-direction: column;
}

.form-group.full {

    grid-column: 1 / -1;
}


.form-group label {

    margin-bottom: 7px;

    font-weight: 600;

    color: #333;
}


.form-group input {

    padding: 12px 14px;

    border: 1px solid #d9dfe4;

    border-radius: 8px;

    font-size: 15px;

    outline: none;

    transition: 0.2s;
}


.form-group input:focus {

    border-color: #0b6fa4;

    box-shadow:
        0 0 0 3px rgba(11,111,164,0.1);
}


/* BUTTON */

.form-actions {

    margin-top: 25px;

    display: flex;

    gap: 12px;
}


.save-btn {

    background: #0b6fa4;

    color: white;

    border: none;

    padding: 12px 25px;

    border-radius: 8px;

    font-size: 15px;

    font-weight: 600;

    cursor: pointer;
}


.save-btn:hover {

    background: #085b85;
}


/* MESSAGE */

.message {

    padding: 14px 18px;

    border-radius: 8px;

    margin-bottom: 20px;

    font-weight: 600;
}


.success {

    background: #e8f8ef;

    color: #16834b;

    border: 1px solid #b9ebce;
}


.error {

    background: #fff0f0;

    color: #c62828;

    border: 1px solid #f3bcbc;
}


/* MOBILE */

@media(max-width:700px) {

    .form-grid {

        grid-template-columns: 1fr;
    }

    .form-group.full {

        grid-column: auto;
    }

    .form-card {

        padding: 20px;
    }

}

</style>

</head>


<body>


<div class="container">


<?php include("sidebar.php"); ?>


<div class="content">


<div class="doctor-page">


<div class="page-header">

<h1>
👨‍⚕️ Add New Doctor
</h1>

<p>
Add a medical professional to your hospital.
</p>

</div>


<?php if ($message != "") { ?>

<div class="message <?php echo $message_type; ?>">

<?php

echo htmlspecialchars($message);

?>

</div>

<?php } ?>


<div class="form-card">


<form method="POST">


<div class="form-grid">


<div class="form-group">

<label>
Full Name
</label>

<input
type="text"
name="fullname"
placeholder="Dr. John Smith"
required>

</div>


<div class="form-group">

<label>
Specialization
</label>

<input
type="text"
name="specialization"
placeholder="Cardiologist"
required>

</div>


<div class="form-group">

<label>
Phone Number
</label>

<input
type="text"
name="phone"
placeholder="9876543210"
required>

</div>


<div class="form-group">

<label>
Email Address
</label>

<input
type="email"
name="email"
placeholder="doctor@example.com"
required>

</div>


<div class="form-group">

<label>
Experience
</label>

<input
type="number"
name="experience"
placeholder="5"
min="0"
max="60"
required>

</div>


</div>


<div class="form-actions">

<button
type="submit"
name="save"
class="save-btn">

➕ Add Doctor

</button>

</div>


</form>


</div>


</div>


</div>


</div>


</body>

</html>