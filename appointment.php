<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include("config.php");

$message = "";

/* Get logged-in patient ID */
$email = $_SESSION['user'];

$userQuery = mysqli_query(
    $conn,
    "SELECT id, fullname FROM users WHERE email='$email'"
);

$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    die("Patient account not found.");
}

$patient_id = $user['id'];
$patient_name = $user['fullname'];


/* Get doctors */
$doctorQuery = mysqli_query(
    $conn,
    "SELECT id, fullname, specialization FROM doctors ORDER BY fullname ASC"
);


/* Book appointment */
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


    /* Check if doctor already has appointment at same date/time */

    $check = mysqli_query(
        $conn,
        "SELECT * FROM appointments
         WHERE doctor_id='$doctor_id'
         AND appointment_date='$appointment_date'
         AND appointment_time='$appointment_time'
         AND status != 'Cancelled'"
    );


    if (mysqli_num_rows($check) > 0) {

        $message = "This doctor is already booked at this date and time.";

    } else {

        $sql = "INSERT INTO appointments
                (patient_id, doctor_id, appointment_date, appointment_time, problem, status)
                VALUES
                ('$patient_id',
                 '$doctor_id',
                 '$appointment_date',
                 '$appointment_time',
                 '$problem',
                 'Pending')";


        if (mysqli_query($conn, $sql)) {

            $message = "Appointment booked successfully!";

        } else {

            $message = "Error: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Book Appointment</title>

<link rel="stylesheet" href="css/appointment.css">

    <link rel="stylesheet" href="css/responsive.css">

</head>


<body>


<div class="appointment-container">


    <div class="appointment-box">


        <h1>🏥 Book Appointment</h1>

        <p class="welcome">
            Welcome, <?php echo htmlspecialchars($patient_name); ?>
        </p>


        <?php

        if ($message != "") {

            echo "<p class='message'>";
            echo htmlspecialchars($message);
            echo "</p>";

        }

        ?>


        <form method="POST">


            <div class="form-group">

                <label>Select Doctor</label>

                <select name="doctor_id" required>

                    <option value="">
                        Select Doctor
                    </option>


                    <?php

                    while ($doctor = mysqli_fetch_assoc($doctorQuery)) {

                    ?>

                        <option value="<?php echo $doctor['id']; ?>">

                            Dr. <?php echo htmlspecialchars($doctor['fullname']); ?>

                            -

                            <?php echo htmlspecialchars($doctor['specialization']); ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

            </div>



            <div class="form-group">

                <label>Appointment Date</label>

                <input
                    type="date"
                    name="appointment_date"
                    min="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label>Appointment Time</label>

                <input
                    type="time"
                    name="appointment_time"
                    required
                >

            </div>



            <div class="form-group">

                <label>Describe Your Problem</label>

                <textarea
                    name="problem"
                    placeholder="Describe your health problem..."
                    required
                ></textarea>

            </div>



            <button type="submit" name="book">

                📅 Book Appointment

            </button>


        </form>


        <div class="back">

            <a href="patient_dashboard.php">
                ← Back to Dashboard
            </a>

        </div>


    </div>


</div>


</body>

</html>