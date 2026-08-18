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
   UPDATE APPOINTMENT STATUS
================================ */

if (isset($_POST['update_status'])) {

    $appointment_id = intval($_POST['appointment_id']);
    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );

    $allowed_status = [
        "Pending",
        "Approved",
        "Rejected",
        "Cancelled"
    ];

    if (in_array($status, $allowed_status)) {

        $update = mysqli_query(
            $conn,
            "UPDATE appointments
             SET status='$status'
             WHERE id='$appointment_id'"
        );

        if ($update) {

            $message = "Appointment status updated successfully.";
            $message_type = "success";

        } else {

            $message = "Unable to update appointment.";
            $message_type = "error";
        }

    } else {

        $message = "Invalid status.";
        $message_type = "error";
    }
}


/* ===============================
   GET ALL APPOINTMENTS
================================ */

$query = "
    SELECT
        appointments.id,
        appointments.patient_id,
        appointments.doctor_id,
        appointments.appointment_date,
        appointments.appointment_time,
        appointments.problem,
        appointments.status,

        users.fullname AS patient_name,
        users.email AS patient_email,
        users.phone AS patient_phone,

        doctors.fullname AS doctor_name,
        doctors.specialization AS doctor_specialization

    FROM appointments

    INNER JOIN users
        ON appointments.patient_id = users.id

    INNER JOIN doctors
        ON appointments.doctor_id = doctors.id

    ORDER BY
        appointments.appointment_date DESC,
        appointments.appointment_time DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Appointment Query Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Appointments</title>

<link rel="stylesheet"
      href="../css/dashboard.css">

<style>

/* ===============================
   PAGE
================================ */

.appointment-content {
    width: 100%;
}

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


/* ===============================
   MESSAGE
================================ */

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


/* ===============================
   TABLE CARD
================================ */

.table-card {
    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);

    overflow-x: auto;
}


/* ===============================
   TABLE
================================ */

.appointment-table {
    width: 100%;

    min-width: 1050px;

    border-collapse: collapse;
}

.appointment-table th {

    background: #0b6fa4;

    color: white;

    padding: 14px;

    text-align: left;

    font-size: 14px;
}

.appointment-table td {

    padding: 14px;

    border-bottom: 1px solid #edf0f2;

    color: #444;

    vertical-align: middle;
}

.appointment-table tr:hover {

    background: #f8fbfd;
}


/* ===============================
   PATIENT / DOCTOR
================================ */

.patient-name,
.doctor-name {

    font-weight: 700;

    color: #0b6fa4;
}

.details {

    display: block;

    color: #777;

    font-size: 12px;

    margin-top: 4px;
}


/* ===============================
   STATUS
================================ */

.status {

    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 700;
}

.pending {

    background: #fff4d6;

    color: #a86b00;
}

.approved {

    background: #e5f8ed;

    color: #16834b;
}

.rejected {

    background: #ffe8e8;

    color: #c62828;
}

.cancelled {

    background: #eeeeee;

    color: #666;
}


/* ===============================
   STATUS FORM
================================ */

.status-form {

    display: flex;

    gap: 7px;

    align-items: center;
}

.status-form select {

    padding: 8px;

    border: 1px solid #ddd;

    border-radius: 6px;

    outline: none;
}

.update-btn {

    border: none;

    background: #0b6fa4;

    color: white;

    padding: 8px 12px;

    border-radius: 6px;

    cursor: pointer;

    font-weight: 600;
}

.update-btn:hover {

    background: #085b85;
}


/* ===============================
   EMPTY
================================ */

.empty {

    text-align: center;

    padding: 60px 20px;

    color: #777;
}

.empty-icon {

    font-size: 55px;

    margin-bottom: 15px;
}

</style>

</head>


<body>


<div class="container">


<!-- SIDEBAR -->

<?php include("sidebar.php"); ?>


<!-- CONTENT -->

<div class="content">

<div class="appointment-content">


<!-- HEADER -->

<div class="page-header">

    <h1>📅 Manage Appointments</h1>

    <p>
        View and manage patient appointments.
    </p>

</div>


<!-- MESSAGE -->

<?php if ($message != "") { ?>

    <div class="message <?php echo $message_type; ?>">

        <?php echo htmlspecialchars($message); ?>

    </div>

<?php } ?>


<!-- TABLE -->

<div class="table-card">


<?php

if (mysqli_num_rows($result) > 0) {

?>

<table class="appointment-table">

<thead>

<tr>

    <th>#</th>

    <th>Patient</th>

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

while ($row = mysqli_fetch_assoc($result)) {

    $status = strtolower($row['status']);

?>

<tr>


<!-- NUMBER -->

<td>

    <?php echo $count++; ?>

</td>


<!-- PATIENT -->

<td>

    <span class="patient-name">

        <?php
        echo htmlspecialchars(
            $row['patient_name']
        );
        ?>

    </span>

    <span class="details">

        <?php
        echo htmlspecialchars(
            $row['patient_email']
        );
        ?>

    </span>

    <span class="details">

        📞
        <?php
        echo htmlspecialchars(
            $row['patient_phone']
        );
        ?>

    </span>

</td>


<!-- DOCTOR -->

<td>

    <span class="doctor-name">

        Dr.
        <?php
        echo htmlspecialchars(
            $row['doctor_name']
        );
        ?>

    </span>

    <span class="details">

        <?php
        echo htmlspecialchars(
            $row['doctor_specialization']
        );
        ?>

    </span>

</td>


<!-- DATE -->

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


<!-- TIME -->

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


<!-- PROBLEM -->

<td>

<?php

echo htmlspecialchars(
    $row['problem']
);

?>

</td>


<!-- STATUS -->

<td>

<?php

if ($status == "pending") {

    echo '<span class="status pending">
            🟡 Pending
          </span>';

} elseif ($status == "approved") {

    echo '<span class="status approved">
            🟢 Approved
          </span>';

} elseif ($status == "rejected") {

    echo '<span class="status rejected">
            🔴 Rejected
          </span>';

} elseif ($status == "cancelled") {

    echo '<span class="status cancelled">
            ⚪ Cancelled
          </span>';

}

?>

</td>


<!-- ACTION -->

<td>

<form method="POST"
      class="status-form">

    <input
        type="hidden"
        name="appointment_id"
        value="<?php echo $row['id']; ?>">

    <select name="status">

        <option value="Pending"
            <?php
            if ($row['status'] == "Pending")
                echo "selected";
            ?>>
            Pending
        </option>

        <option value="Approved"
            <?php
            if ($row['status'] == "Approved")
                echo "selected";
            ?>>
            Approved
        </option>

        <option value="Rejected"
            <?php
            if ($row['status'] == "Rejected")
                echo "selected";
            ?>>
            Rejected
        </option>

        <option value="Cancelled"
            <?php
            if ($row['status'] == "Cancelled")
                echo "selected";
            ?>>
            Cancelled
        </option>

    </select>


    <button
        type="submit"
        name="update_status"
        class="update-btn">

        Update

    </button>

</form>

</td>


</tr>

<?php } ?>


</tbody>

</table>


<?php

} else {

?>

<div class="empty">

    <div class="empty-icon">
        📅
    </div>

    <h2>
        No Appointments Found
    </h2>

    <p>
        There are currently no patient appointments.
    </p>

</div>

<?php } ?>


</div>

</div>

</div>

</div>


</body>

</html>