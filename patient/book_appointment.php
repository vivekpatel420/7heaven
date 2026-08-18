<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$user_email = $_SESSION['user'];

$message = "";
$message_type = "";


/* ===============================
   GET PATIENT INFORMATION
================================ */

$userQuery = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email='$user_email'"
);

$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$patient_id = $user['id'];
$fullname = $user['fullname'];


/* ===============================
   BOOK APPOINTMENT
================================ */

if (isset($_POST['book'])) {

    $doctor_id = mysqli_real_escape_string(
        $conn,
        $_POST['doctor_id']
    );

    $appointment_date = mysqli_real_escape_string(
        $conn,
        $_POST['appointment_date']
    );

    $appointment_time = mysqli_real_escape_string(
        $conn,
        $_POST['appointment_time']
    );

    $problem = mysqli_real_escape_string(
        $conn,
        $_POST['problem']
    );


    /* Check doctor */

    $doctorCheck = mysqli_query(
        $conn,
        "SELECT id FROM doctors WHERE id='$doctor_id'"
    );

    if (mysqli_num_rows($doctorCheck) == 0) {

        $message = "Please select a valid doctor.";
        $message_type = "error";

    } else {

        /* Check date */

        if ($appointment_date < date("Y-m-d")) {

            $message = "Please select a future date.";
            $message_type = "error";

        } else {

            /* Check same doctor, date and time */

            $slotCheck = mysqli_query(
                $conn,
                "SELECT id FROM appointments
                 WHERE doctor_id='$doctor_id'
                 AND appointment_date='$appointment_date'
                 AND appointment_time='$appointment_time'
                 AND status!='Cancelled'"
            );


            if (mysqli_num_rows($slotCheck) > 0) {

                $message = "This appointment slot is already booked.";
                $message_type = "error";

            } else {

                /* Insert appointment */

                $sql = "INSERT INTO appointments
                        (
                            patient_id,
                            doctor_id,
                            appointment_date,
                            appointment_time,
                            problem,
                            status
                        )
                        VALUES
                        (
                            '$patient_id',
                            '$doctor_id',
                            '$appointment_date',
                            '$appointment_time',
                            '$problem',
                            'Pending'
                        )";


                if (mysqli_query($conn, $sql)) {

                    $message = "Appointment booked successfully!";
                    $message_type = "success";

                } else {

                    $message = "Error: " . mysqli_error($conn);
                    $message_type = "error";
                }
            }
        }
    }
}


/* ===============================
   GET DOCTORS
================================ */

$doctors = mysqli_query(
    $conn,
    "SELECT * FROM doctors ORDER BY fullname ASC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Book Appointment</title>

<link rel="stylesheet"
      href="dashboard.css">

<style>

/* ===============================
   PAGE
================================ */

.appointment-page {
    max-width: 900px;
    margin: 0 auto;
}


/* ===============================
   HEADER
================================ */

.page-header {
    margin-bottom: 25px;
}

.page-header h1 {
    margin: 0 0 8px;
    color: #0b6fa4;
}

.page-header p {
    color: #666;
    margin: 0;
}


/* ===============================
   FORM CARD
================================ */

.appointment-card {
    background: white;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}


/* ===============================
   FORM
================================ */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #d5dce3;

    border-radius: 8px;

    font-size: 15px;

    outline: none;

    transition: 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {

    border-color: #0b6fa4;

    box-shadow: 0 0 0 3px rgba(11,111,164,0.1);
}

.form-group textarea {

    min-height: 120px;

    resize: vertical;
}


/* ===============================
   TWO COLUMN FORM
================================ */

.form-row {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 20px;
}


/* ===============================
   BUTTON
================================ */

.book-btn {

    width: 100%;

    border: none;

    background: #0b6fa4;

    color: white;

    padding: 14px;

    border-radius: 8px;

    font-size: 16px;

    font-weight: 600;

    cursor: pointer;

    transition: 0.3s;
}

.book-btn:hover {

    background: #085b85;

    transform: translateY(-1px);
}


/* ===============================
   MESSAGE
================================ */

.message {

    padding: 13px 15px;

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


/* ===============================
   INFO
================================ */

.appointment-info {

    background: #eef8fc;

    border-left: 4px solid #0b6fa4;

    padding: 15px;

    margin-bottom: 25px;

    border-radius: 6px;

    color: #444;
}


/* ===============================
   MOBILE
================================ */

@media(max-width:700px) {

    .appointment-card {
        padding: 22px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

}

</style>

    <link rel="stylesheet" href="../css/responsive.css">

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            🏥
        </div>

        <h2>7 Heaven</h2>

        <p>Patient Portal</p>

    </div>


    <nav>

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="book_appointment.php"
           class="active">
            📅 Book Appointment
        </a>

        <a href="my_appointments.php">
            📋 My Appointments
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

    </nav>


    <div class="sidebar-bottom">

        <a href="logout.php"
           class="logout">
            🚪 Logout
        </a>

    </div>

</div>


<!-- MAIN -->

<div class="main">


    <div class="appointment-page">


        <!-- HEADER -->

        <div class="page-header">

            <h1>📅 Book Appointment</h1>

            <p>
                Schedule an appointment with one of our doctors.
            </p>

        </div>


        <!-- MESSAGE -->

        <?php if ($message != "") { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <!-- INFO -->

        <div class="appointment-info">

            👋 Hello
            <strong>
                <?php echo htmlspecialchars($fullname); ?>
            </strong>.

            Please select your doctor and preferred appointment
            date and time.

        </div>


        <!-- FORM -->

        <div class="appointment-card">

            <form method="POST">


                <!-- DOCTOR -->

                <div class="form-group">

                    <label>
                        Select Doctor
                    </label>

                    <select name="doctor_id"
                            required>

                        <option value="">
                            -- Select Doctor --
                        </option>


                        <?php

                        if (mysqli_num_rows($doctors) > 0) {

                            while ($doctor = mysqli_fetch_assoc($doctors)) {

                        ?>

                            <option value="<?php echo $doctor['id']; ?>">

                                Dr.
                                <?php echo htmlspecialchars($doctor['fullname']); ?>

                                -
                                <?php echo htmlspecialchars($doctor['specialization']); ?>

                            </option>

                        <?php

                            }

                        } else {

                        ?>

                            <option value="">
                                No doctors available
                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- DATE + TIME -->

                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Appointment Date
                        </label>

                        <input
                            type="date"
                            name="appointment_date"
                            min="<?php echo date('Y-m-d'); ?>"
                            required>

                    </div>


                    <div class="form-group">

                        <label>
                            Appointment Time
                        </label>

                        <input
                            type="time"
                            name="appointment_time"
                            required>

                    </div>


                </div>


                <!-- PROBLEM -->

                <div class="form-group">

                    <label>
                        Describe Your Problem
                    </label>

                    <textarea
                        name="problem"
                        placeholder="Please describe your health problem..."
                        required></textarea>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    name="book"
                    class="book-btn">

                    📅 Book Appointment

                </button>


            </form>

        </div>


    </div>

</div>


</body>

</html>