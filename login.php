<?php

session_start();

include("config.php");

$message = "";


/* =================================
   PATIENT LOGIN
================================= */

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];


    /* Find patient by email */

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, fullname, email, password
         FROM users
         WHERE email = ?"
    );


    if (!$stmt) {

        $message = "Database error. Please try again.";

    } else {

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);


        /* Check account */

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);


            /* Check password */

            if ($password === $user['password']) {

                /*
                 * Save patient email in session.
                 * This is used by patient/dashboard.php
                 */

                $_SESSION['user'] = $user['email'];


                /* Patient ONLY */

                header("Location: patient/dashboard.php");
                exit();

            } else {

                $message = "Invalid Email or Password!";

            }

        } else {

            $message = "Invalid Email or Password!";

        }


        mysqli_stmt_close($stmt);
    }
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

<title>Patient Login | 7 Heaven Hospital</title>

<link
    rel="stylesheet"
    href="css/login.css"
>

    <link rel="stylesheet" href="css/responsive.css">

</head>


<body>


<div class="login-container">


    <div class="login-box">


        <!-- LOGO -->

        <div class="login-icon">
            🏥
        </div>


        <h2>
            Patient Login
        </h2>


        <p class="login-subtitle">
            Welcome back! Login to your patient account.
        </p>


        <!-- ERROR -->

        <?php if ($message != "") { ?>

            <div class="error-message">

                ❌
                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php } ?>


        <!-- LOGIN FORM -->

        <form method="POST">


            <div class="input-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="input-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <button
                type="submit"
                name="login"
            >

                Login

            </button>


        </form>


        <p class="register">

            Don't have an account?

            <a href="register.php">
                Register Here
            </a>

        </p>


        <a
            href="index.php"
            class="back-home"
        >

            ← Back to Home

        </a>


    </div>

</div>


</body>

</html>