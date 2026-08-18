<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$result = mysqli_query($conn, "SELECT * FROM patients ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>

<title>View Patients</title>

<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/forms.css">

<style>

.table-card{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
    width:100%;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th{
    background:#0b6fa4;
    color:white;
    padding:14px;
}

table td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

tr:hover{
    background:#f5f5f5;
}

.edit-btn{
    background:#28a745;
    color:white;
    padding:8px 14px;
    text-decoration:none;
    border-radius:5px;
}

.delete-btn{
    background:#dc3545;
    color:white;
    padding:8px 14px;
    text-decoration:none;
    border-radius:5px;
}

.edit-btn:hover{
    background:#218838;
}

.delete-btn:hover{
    background:#c82333;
}

</style>

</head>

<body>

<div class="container">

<?php include("sidebar.php"); ?>

<div class="content">

<div class="table-card">

<h1>Patients List</h1>

<table>

<tr>

<th>ID</th>
<th>Name</th>
<th>Gender</th>
<th>DOB</th>
<th>Blood</th>
<th>Phone</th>
<th>Email</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['fullname']; ?></td>

<td><?php echo $row['gender']; ?></td>

<td><?php echo $row['dob']; ?></td>

<td><?php echo $row['blood_group']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>

<a class="edit-btn"
href="edit_patient.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="delete-btn"
onclick="return confirm('Delete this patient?')"
href="delete_patient.php?id=<?php echo $row['id']; ?>">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>

</html>