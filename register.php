<?php
include("config.php");

$message = "";

if(isset($_POST['register']))
{
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0)
    {
        $message = "Email already registered!";
    }
    else
    {
        $sql = "INSERT INTO users(fullname,email,phone,password)
                VALUES('$fullname','$email','$phone','$password')";

        if(mysqli_query($conn,$sql))
        {
            $message = "Registration Successful! Please Login.";
        }
        else
        {
            $message = "Error: ".mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Patient Registration</title>

<link rel="stylesheet" href="css/register.css">

    <link rel="stylesheet" href="css/responsive.css">

</head>

<body>

<div class="register-box">

<h2>🏥 Patient Registration</h2>

<?php
if($message!=""){
echo "<p class='message'>$message</p>";
}
?>

<form method="POST">

<input type="text" name="fullname" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="text" name="phone" placeholder="Phone Number" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="register">
Create Account
</button>

</form>

<p>
Already have an account?

<a href="login.php">Login Here</a>

</p>

</div>

</body>
</html>