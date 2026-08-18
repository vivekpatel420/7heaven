<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config.php");

$message = "";
$message_type = "";


/* =========================================
   ADD AVAILABILITY
========================================= */

if (isset($_POST['save'])) {

    $doctor_id = intval($_POST['doctor_id']);
    $day = mysqli_real_escape_string(
        $conn,
        $_POST['day_of_week']
    );

    $start_time = mysqli_real_escape_string(
        $conn,
        $_POST['start_time']
    );

    $end_time = mysqli_real_escape_string(
        $conn,
        $_POST['end_time']
    );

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );


    /* Check doctor */

    $doctorCheck = mysqli_query(
        $conn,
        "SELECT id FROM doctors WHERE id='$doctor_id'"
    );


    if (!$doctorCheck || mysqli_num_rows($doctorCheck) == 0) {

        $message = "Please select a valid doctor.";
        $message_type = "error";

    }

    /* Check time */

    elseif ($start_time >= $end_time) {

        $message = "End time must be after start time.";
        $message_type = "error";

    }

    else {

        /* Check duplicate schedule */

        $duplicate = mysqli_query(
            $conn,
            "SELECT id
             FROM doctor_availability
             WHERE doctor_id='$doctor_id'
             AND day_of_week='$day'
             AND start_time='$start_time'
             AND end_time='$end_time'"
        );


        if (mysqli_num_rows($duplicate) > 0) {

            $message =
                "This schedule already exists.";

            $message_type = "error";

        }

        else {

            $sql = "
                INSERT INTO doctor_availability
                (
                    doctor_id,
                    day_of_week,
                    start_time,
                    end_time,
                    status
                )

                VALUES
                (
                    '$doctor_id',
                    '$day',
                    '$start_time',
                    '$end_time',
                    '$status'
                )
            ";


            if (mysqli_query($conn, $sql)) {

                $message =
                    "Doctor availability added successfully.";

                $message_type = "success";

            }

            else {

                $message =
                    "Error: " .
                    mysqli_error($conn);

                $message_type = "error";
            }
        }
    }
}


/* =========================================
   DELETE AVAILABILITY
========================================= */

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $delete = mysqli_query(
        $conn,
        "DELETE FROM doctor_availability
         WHERE id='$id'"
    );


    if ($delete) {

        header("Location: availability.php");
        exit();

    }

    else {

        $message =
            "Unable to delete schedule.";

        $message_type = "error";
    }
}


/* =========================================
   GET DOCTORS
========================================= */

$doctors = mysqli_query(
    $conn,
    "SELECT id, fullname, specialization
     FROM doctors
     ORDER BY fullname ASC"
);


if (!$doctors) {

    die(
        "Doctor Query Error: " .
        mysqli_error($conn)
    );

}


/* =========================================
   GET AVAILABILITY
========================================= */

$schedules = mysqli_query(
    $conn,

    "SELECT
        doctor_availability.id,
        doctor_availability.day_of_week,
        doctor_availability.start_time,
        doctor_availability.end_time,
        doctor_availability.status,

        doctors.fullname,
        doctors.specialization

     FROM doctor_availability

     INNER JOIN doctors
        ON doctor_availability.doctor_id = doctors.id

     ORDER BY
        doctors.fullname ASC,
        doctor_availability.id DESC"
);


if (!$schedules) {

    die(
        "Availability Query Error: " .
        mysqli_error($conn)
    );

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Doctor Availability</title>


<link
    rel="stylesheet"
    href="../css/dashboard.css"
>


<style>

/* =====================================
   PAGE
===================================== */

.availability-page {

    width: 100%;

    max-width: 1200px;
}


/* =====================================
   HEADER
===================================== */

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

    flex-wrap: wrap;

    gap: 15px;
}


.page-header h1 {

    color: #0b6fa4;

    margin-bottom: 6px;
}


.page-header p {

    color: #666;

    margin: 0;
}


/* =====================================
   FORM CARD
===================================== */

.form-card {

    background: white;

    padding: 28px;

    border-radius: 16px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);

    margin-bottom: 30px;
}


.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;
}


.form-group {

    display: flex;

    flex-direction: column;
}


.form-group label {

    margin-bottom: 7px;

    font-weight: 600;

    color: #333;
}


.form-group select,
.form-group input {

    padding: 12px 14px;

    border: 1px solid #d9dfe4;

    border-radius: 8px;

    font-size: 15px;

    outline: none;

    background: white;
}


.form-group select:focus,
.form-group input:focus {

    border-color: #0b6fa4;

    box-shadow:
        0 0 0 3px rgba(11,111,164,0.1);
}


/* =====================================
   BUTTON
===================================== */

.form-actions {

    margin-top: 25px;
}


.save-btn {

    border: none;

    background: #0b6fa4;

    color: white;

    padding: 12px 25px;

    border-radius: 8px;

    font-size: 15px;

    font-weight: 600;

    cursor: pointer;
}


.save-btn:hover {

    background: #085b85;
}


/* =====================================
   MESSAGE
===================================== */

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


/* =====================================
   TABLE CARD
===================================== */

.table-card {

    background: white;

    padding: 25px;

    border-radius: 16px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);

    overflow-x: auto;
}


.table-card h2 {

    color: #0b6fa4;

    margin-bottom: 20px;
}


/* =====================================
   TABLE
===================================== */

.availability-table {

    width: 100%;

    min-width: 800px;

    border-collapse: collapse;
}


.availability-table th {

    background: #0b6fa4;

    color: white;

    padding: 14px;

    text-align: left;

    font-size: 14px;
}


.availability-table td {

    padding: 14px;

    border-bottom: 1px solid #edf0f2;

    color: #444;
}


.availability-table tr:hover {

    background: #f8fbfd;
}


/* =====================================
   DOCTOR
===================================== */

.doctor-name {

    font-weight: 700;

    color: #0b6fa4;
}


.specialization {

    display: block;

    margin-top: 4px;

    color: #777;

    font-size: 13px;
}


/* =====================================
   STATUS
===================================== */

.status {

    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 700;
}


.available {

    background: #e5f8ed;

    color: #16834b;
}


.unavailable {

    background: #ffe8e8;

    color: #c62828;
}


/* =====================================
   DELETE BUTTON
===================================== */

.delete-btn {

    text-decoration: none;

    background: #ffe8e8;

    color: #c62828;

    padding: 7px 12px;

    border-radius: 6px;

    font-size: 13px;

    font-weight: 600;
}


.delete-btn:hover {

    background: #ffd6d6;
}


/* =====================================
   EMPTY
===================================== */

.empty {

    text-align: center;

    padding: 50px 20px;

    color: #777;
}


.empty-icon {

    font-size: 50px;

    margin-bottom: 12px;
}


/* =====================================
   MOBILE
===================================== */

@media(max-width:700px) {

    .form-grid {

        grid-template-columns: 1fr;
    }

    .form-card {

        padding: 20px;
    }

    .table-card {

        padding: 15px;
    }

}

</style>

</head>


<body>


<div class="container">


<!-- SIDEBAR -->

<?php include("sidebar.php"); ?>


<!-- CONTENT -->

<div class="content">


<div class="availability-page">


<!-- HEADER -->

<div class="page-header">

<div>

<h1>
🕐 Doctor Availability
</h1>

<p>
Manage doctors' working days and consultation timings.
</p>

</div>

</div>


<!-- MESSAGE -->

<?php if ($message != "") { ?>

<div
    class="message <?php echo $message_type; ?>"
>

<?php

echo htmlspecialchars($message);

?>

</div>

<?php } ?>


<!-- =================================
     ADD AVAILABILITY FORM
================================= -->

<div class="form-card">


<h2>
➕ Add Doctor Schedule
</h2>


<br>


<form method="POST">


<div class="form-grid">


<!-- DOCTOR -->

<div class="form-group">

<label>
👨‍⚕️ Select Doctor
</label>

<select
    name="doctor_id"
    required
>

<option value="">
Select Doctor
</option>


<?php

while ($doctor = mysqli_fetch_assoc($doctors)) {

?>

<option
    value="<?php
        echo $doctor['id'];
    ?>"
>

Dr.
<?php

echo htmlspecialchars(
    $doctor['fullname']
);

?>

-
<?php

echo htmlspecialchars(
    $doctor['specialization']
);

?>

</option>

<?php

}

?>

</select>

</div>


<!-- DAY -->

<div class="form-group">

<label>
📅 Day
</label>

<select
    name="day_of_week"
    required
>

<option value="">
Select Day
</option>

<option value="Monday">
Monday
</option>

<option value="Tuesday">
Tuesday
</option>

<option value="Wednesday">
Wednesday
</option>

<option value="Thursday">
Thursday
</option>

<option value="Friday">
Friday
</option>

<option value="Saturday">
Saturday
</option>

<option value="Sunday">
Sunday
</option>

</select>

</div>


<!-- START TIME -->

<div class="form-group">

<label>
🕐 Start Time
</label>

<input
    type="time"
    name="start_time"
    required
>

</div>


<!-- END TIME -->

<div class="form-group">

<label>
🕐 End Time
</label>

<input
    type="time"
    name="end_time"
    required
>

</div>


<!-- STATUS -->

<div class="form-group">

<label>
⚡ Availability Status
</label>

<select
    name="status"
    required
>

<option value="Available">
🟢 Available
</option>

<option value="Unavailable">
🔴 Unavailable
</option>

</select>

</div>


</div>


<div class="form-actions">

<button
    type="submit"
    name="save"
    class="save-btn"
>

💾 Save Schedule

</button>

</div>


</form>


</div>



<!-- =================================
     EXISTING SCHEDULES
================================= -->

<div class="table-card">


<h2>
📋 Doctor Schedules
</h2>


<?php

if (mysqli_num_rows($schedules) > 0) {

?>


<table class="availability-table">


<thead>

<tr>

<th>#</th>

<th>Doctor</th>

<th>Day</th>

<th>Start Time</th>

<th>End Time</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>


<tbody>


<?php

$count = 1;


while (
    $schedule =
    mysqli_fetch_assoc($schedules)
) {

?>


<tr>


<td>

<?php

echo $count++;

?>

</td>


<td>

<span class="doctor-name">

Dr.
<?php

echo htmlspecialchars(
    $schedule['fullname']
);

?>

</span>


<span class="specialization">

<?php

echo htmlspecialchars(
    $schedule['specialization']
);

?>

</span>

</td>


<td>

📅
<?php

echo htmlspecialchars(
    $schedule['day_of_week']
);

?>

</td>


<td>

<?php

echo date(
    "h:i A",
    strtotime(
        $schedule['start_time']
    )
);

?>

</td>


<td>

<?php

echo date(
    "h:i A",
    strtotime(
        $schedule['end_time']
    )
);

?>

</td>


<td>


<?php

if (
    $schedule['status']
    == "Available"
) {

?>

<span class="status available">

🟢 Available

</span>

<?php

} else {

?>

<span class="status unavailable">

🔴 Unavailable

</span>

<?php

}

?>


</td>


<td>

<a
    href="availability.php?delete=<?php
        echo $schedule['id'];
    ?>"
    class="delete-btn"
    onclick="return confirm(
        'Are you sure you want to delete this schedule?'
    );"
>

🗑️ Delete

</a>

</td>


</tr>


<?php

}

?>


</tbody>

</table>


<?php

} else {

?>


<div class="empty">

<div class="empty-icon">
📅
</div>

<h3>
No schedules found
</h3>

<p>
Add a doctor's working schedule above.
</p>

</div>


<?php

}

?>


</div>


</div>


</div>


</div>


</body>

</html>