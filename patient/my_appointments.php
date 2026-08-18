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
   GET PATIENT
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
   CANCEL APPOINTMENT
================================ */

if (isset($_POST['cancel'])) {

    $appointment_id = intval($_POST['appointment_id']);

    $cancelQuery = mysqli_query(
        $conn,
        "UPDATE appointments
         SET status='Cancelled'
         WHERE id='$appointment_id'
         AND patient_id='$patient_id'
         AND status='Pending'"
    );

    if ($cancelQuery && mysqli_affected_rows($conn) > 0) {

        $message = "Appointment cancelled successfully.";
        $message_type = "success";

    } else {

        $message = "Unable to cancel this appointment.";
        $message_type = "error";
    }
}


/* ===============================
   GET APPOINTMENTS
================================ */

$appointments = mysqli_query(
    $conn,
    "SELECT
        appointments.*,
        doctors.fullname AS doctor_name,
        doctors.specialization

     FROM appointments

     INNER JOIN doctors
     ON appointments.doctor_id = doctors.id

     WHERE appointments.patient_id='$patient_id'

     ORDER BY
        appointments.appointment_date DESC,
        appointments.appointment_time DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Appointments</title>

<link rel="stylesheet"
      href="dashboard.css">

<style>

/* =====================================
   APPOINTMENTS PAGE
===================================== */

.appointment-page {
    max-width: 1200px;
    margin: 0 auto;
}


/* HEADER */

.page-header {
    margin-bottom: 25px;
}

.page-header h1 {
    color: #0b6fa4;
    margin-bottom: 8px;
}

.page-header p {
    color: #666;
    margin: 0;
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


/* TABLE CARD */

.appointment-card {
    background: white;
    border-radius: 15px;
    padding: 25px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);

    overflow-x: auto;
}


/* TABLE */

.appointment-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.appointment-table th {
    background: #0b6fa4;
    color: white;

    padding: 14px 12px;

    text-align: left;

    font-size: 14px;
}

.appointment-table td {
    padding: 15px 12px;

    border-bottom: 1px solid #edf0f2;

    color: #444;

    vertical-align: middle;
}

.appointment-table tr:hover {
    background: #f8fbfd;
}


/* DOCTOR */

.doctor-name {
    font-weight: 700;
    color: #0b6fa4;
}

.specialization {
    display: block;
    color: #777;
    font-size: 13px;
    margin-top: 4px;
}


/* STATUS */

.status {
    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 700;
}

.status-pending {
    background: #fff4d6;
    color: #a86b00;
}

.status-approved {
    background: #e5f8ed;
    color: #16834b;
}

.status-cancelled {
    background: #ffe8e8;
    color: #c62828;
}

.status-rejected {
    background: #ffe8e8;
    color: #c62828;
}


/* CANCEL BUTTON */

.cancel-btn {
    border: none;

    background: #e74c3c;

    color: white;

    padding: 8px 13px;

    border-radius: 6px;

    cursor: pointer;

    font-size: 13px;

    font-weight: 600;

    transition: 0.3s;
}

.cancel-btn:hover {
    background: #c0392b;
}


/* NO APPOINTMENTS */

.no-appointments {
    text-align: center;

    padding: 55px 20px;
}

.no-appointments .icon {
    font-size: 55px;

    margin-bottom: 15px;
}

.no-appointments h2 {
    color: #333;

    margin-bottom: 10px;
}

.no-appointments p {
    color: #777;

    margin-bottom: 20px;
}

.book-btn {
    display: inline-block;

    text-decoration: none;

    background: #0b6fa4;

    color: white;

    padding: 12px 20px;

    border-radius: 7px;

    font-weight: 600;

    transition: 0.3s;
}

.book-btn:hover {
    background: #085b85;
}


/* MOBILE */

@media(max-width: 700px) {

    .appointment-card {
        padding: 15px;
    }

    .page-header h1 {
        font-size: 25px;
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

        <a href="book_appointment.php">
            📅 Book Appointment
        </a>

        <a href="my_appointments.php"
           class="active">
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

            <h1>
                📋 My Appointments
            </h1>

            <p>
                View and manage your hospital appointments.
            </p>

        </div>


        <!-- MESSAGE -->

        <?php if ($message != "") { ?>

            <div class="message <?php echo $message_type; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <!-- APPOINTMENT CARD -->

        <div class="appointment-card">

        <?php

        if (mysqli_num_rows($appointments) > 0) {

        ?>

            <table class="appointment-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Doctor</th>

                        <th>Date</th>

                        <th>Time</th>

                        <th>Problem</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $count = 1;

                while ($row = mysqli_fetch_assoc($appointments)) {

                    $status = strtolower($row['status']);

                ?>

                    <tr>

                        <td>
                            <?php echo $count++; ?>
                        </td>


                        <td>

                            <span class="doctor-name">

                                Dr.
                                <?php
                                echo htmlspecialchars(
                                    $row['doctor_name']
                                );
                                ?>

                            </span>

                            <span class="specialization">

                                <?php
                                echo htmlspecialchars(
                                    $row['specialization']
                                );
                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $row['appointment_date']
                                )
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo date(
                                "h:i A",
                                strtotime(
                                    $row['appointment_time']
                                )
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row['problem']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            if ($status == "pending") {

                                echo '<span class="status status-pending">
                                        🟡 Pending
                                      </span>';

                            } elseif ($status == "approved") {

                                echo '<span class="status status-approved">
                                        🟢 Approved
                                      </span>';

                            } elseif ($status == "cancelled") {

                                echo '<span class="status status-cancelled">
                                        🔴 Cancelled
                                      </span>';

                            } elseif ($status == "rejected") {

                                echo '<span class="status status-rejected">
                                        🔴 Rejected
                                      </span>';

                            } else {

                                echo '<span class="status status-pending">'
                                     . htmlspecialchars($row['status']) .
                                     '</span>';
                            }

                            ?>

                        </td>


                        <td>

                            <?php

                            if ($status == "pending") {

                            ?>

                                <form method="POST"
                                      onsubmit="return confirm('Are you sure you want to cancel this appointment?');">

                                    <input
                                        type="hidden"
                                        name="appointment_id"
                                        value="<?php echo $row['id']; ?>">

                                    <button
                                        type="submit"
                                        name="cancel"
                                        class="cancel-btn">

                                        Cancel

                                    </button>

                                </form>

                            <?php

                            } else {

                                echo "<span style='color:#999;'>—</span>";

                            }

                            ?>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php

        } else {

        ?>

            <div class="no-appointments">

                <div class="icon">
                    📅
                </div>

                <h2>
                    No Appointments Yet
                </h2>

                <p>
                    You haven't booked any appointments.
                </p>

                <a href="book_appointment.php"
                   class="book-btn">

                    📅 Book Your First Appointment

                </a>

            </div>

        <?php } ?>

        </div>

    </div>

</div>


</body>

</html>