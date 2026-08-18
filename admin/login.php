<?php
session_start();

include("../config.php");

$message = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM admin 
              WHERE email='$email' 
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {

        $_SESSION['admin'] = $email;

        header("Location: dashboard.php");
        exit();

    } else {

        $message = "Invalid Admin Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - MediCare</title>

    <link rel="stylesheet" href="admin_login.css">

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <div class="logo">
            🏥
        </div>

        <h1>Admin Login</h1>

        <p class="subtitle">
            7 Heaven Hospital Management System
        </p>

        <?php if ($message != "") { ?>

            <div class="error">
                <?php echo $message; ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter admin email"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                >

            </div>

            <button type="submit" name="login">
                Login to Dashboard
            </button>

        </form>

        <p class="back-link">
          7 Heaven Hospital Management System
        </p>

    </div>

</div>

</body>

</html>