<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config.php");


$result = mysqli_query(
    $conn,
    "SELECT * FROM doctors ORDER BY id DESC"
);


if (!$result) {

    die(
        "Doctor Query Error: " .
        mysqli_error($conn)
    );

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Doctors</title>

<link rel="stylesheet"
      href="../css/dashboard.css">


<style>

/* ==============================
   PAGE
============================== */

.doctor-page {

    width: 100%;
}


/* HEADER */

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

    gap: 15px;

    flex-wrap: wrap;
}


.page-header h1 {

    color: #0b6fa4;

    margin-bottom: 6px;
}


.page-header p {

    color: #666;

    margin: 0;
}


/* ADD BUTTON */

.add-btn {

    text-decoration: none;

    background: #0b6fa4;

    color: white;

    padding: 11px 18px;

    border-radius: 8px;

    font-weight: 600;
}


.add-btn:hover {

    background: #085b85;
}


/* ==============================
   TABLE CARD
============================== */

.table-card {

    background: white;

    padding: 25px;

    border-radius: 16px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.07);

    overflow-x: auto;
}


/* TABLE */

.doctor-table {

    width: 100%;

    min-width: 850px;

    border-collapse: collapse;
}


.doctor-table th {

    background: #0b6fa4;

    color: white;

    padding: 14px;

    text-align: left;

    font-size: 14px;
}


.doctor-table td {

    padding: 15px 14px;

    border-bottom: 1px solid #edf0f2;

    color: #444;
}


.doctor-table tr:hover {

    background: #f8fbfd;
}


/* DOCTOR */

.doctor-name {

    font-weight: 700;

    color: #0b6fa4;
}


.specialization {

    color: #777;

    font-size: 13px;

    margin-top: 4px;

    display: block;
}


/* EXPERIENCE */

.experience {

    font-weight: 600;
}


/* ACTIONS */

.actions {

    display: flex;

    gap: 7px;
}


.edit-btn,
.delete-btn {

    text-decoration: none;

    padding: 7px 11px;

    border-radius: 6px;

    font-size: 13px;

    font-weight: 600;
}


.edit-btn {

    background: #e8f3f9;

    color: #0b6fa4;
}


.delete-btn {

    background: #ffe8e8;

    color: #c62828;
}


.edit-btn:hover {

    background: #d8eaf3;
}


.delete-btn:hover {

    background: #ffd6d6;
}


/* EMPTY */

.empty {

    text-align: center;

    padding: 55px 20px;

    color: #777;
}


.empty-icon {

    font-size: 55px;

    margin-bottom: 15px;
}


/* MOBILE */

@media(max-width:700px) {

    .table-card {

        padding: 15px;
    }

}

</style>

</head>


<body>


<div class="container">


<?php include("sidebar.php"); ?>


<div class="content">


<div class="doctor-page">


<!-- HEADER -->

<div class="page-header">

<div>

<h1>
👨‍⚕️ Doctors
</h1>

<p>
Manage hospital doctors and specialists.
</p>

</div>


<a
href="doctors.php"
class="add-btn">

➕ Add Doctor

</a>

</div>


<!-- TABLE -->

<div class="table-card">


<?php

if (mysqli_num_rows($result) > 0) {

?>


<table class="doctor-table">


<thead>

<tr>

<th>#</th>

<th>Doctor</th>

<th>Phone</th>

<th>Email</th>

<th>Experience</th>

<th>Actions</th>

</tr>

</thead>


<tbody>


<?php

$count = 1;

while ($doctor = mysqli_fetch_assoc($result)) {

?>


<tr>


<td>

<?php echo $count++; ?>

</td>


<td>

<div class="doctor-name">

Dr.
<?php

echo htmlspecialchars(
    $doctor['fullname']
);

?>

</div>


<span class="specialization">

<?php

echo htmlspecialchars(
    $doctor['specialization']
);

?>

</span>

</td>


<td>

<?php

echo htmlspecialchars(
    $doctor['phone']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $doctor['email']
);

?>

</td>


<td>

<span class="experience">

<?php

echo htmlspecialchars(
    $doctor['experience']
);

?>
 years

</span>

</td>


<td>

<div class="actions">


<a
href="edit_doctor.php?id=<?php echo $doctor['id']; ?>"
class="edit-btn">

✏️ Edit

</a>


<a
href="delete_doctor.php?id=<?php echo $doctor['id']; ?>"
class="delete-btn"

onclick="return confirm(
'Are you sure you want to delete this doctor?'
);">

🗑️ Delete

</a>


</div>

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
👨‍⚕️
</div>

<h2>
No Doctors Found
</h2>

<p>
Add your first doctor to the system.
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