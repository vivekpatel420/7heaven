```php
<?php
session_start();

// Check whether the patient is logged in
if (!isset($_SESSION['user']) || !is_string($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$user_email = $_SESSION['user'];

// Get patient information securely
$userQuery = mysqli_prepare(
    $conn,
    "SELECT id, fullname FROM users WHERE email = ? LIMIT 1"
);

if (!$userQuery) {
    die("Unable to load patient information.");
}

mysqli_stmt_bind_param($userQuery, "s", $user_email);
mysqli_stmt_execute($userQuery);

$userResult = mysqli_stmt_get_result($userQuery);
$user = mysqli_fetch_assoc($userResult);

mysqli_stmt_close($userQuery);

// If the patient account does not exist, log out
if (!$user) {
    session_unset();
    session_destroy();

    header("Location: ../login.php");
    exit();
}

$patient_id = (int) $user['id'];
$fullname = $user['fullname'];

// Default appointment counts
$totalAppointments = 0;
$pendingAppointments = 0;

// Count all appointments
$appointmentQuery = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM appointments
     WHERE patient_id = ?"
);

if ($appointmentQuery) {
    mysqli_stmt_bind_param($appointmentQuery, "i", $patient_id);
    mysqli_stmt_execute($appointmentQuery);

    $appointmentResult = mysqli_stmt_get_result($appointmentQuery);
    $appointmentData = mysqli_fetch_assoc($appointmentResult);

    if ($appointmentData) {
        $totalAppointments = (int) $appointmentData['total'];
    }

    mysqli_stmt_close($appointmentQuery);
}

// Count pending appointments
$pendingQuery = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM appointments
     WHERE patient_id = ?
     AND status = 'Pending'"
);

if ($pendingQuery) {
    mysqli_stmt_bind_param($pendingQuery, "i", $patient_id);
    mysqli_stmt_execute($pendingQuery);

    $pendingResult = mysqli_stmt_get_result($pendingQuery);
    $pendingData = mysqli_fetch_assoc($pendingResult);

    if ($pendingData) {
        $pendingAppointments = (int) $pendingData['total'];
    }

    mysqli_stmt_close($pendingQuery);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Dashboard - 7 Heaven</title>

    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="../css/responsive.css">
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="logo">
            <div class="logo-icon">🏥</div>
            <h2>7 Heaven</h2>
        </div>

        <nav>
            <a href="dashboard.php" class="active">
                <span>🏠</span>
                Dashboard
            </a>

            <a href="book_appointment.php">
                <span>📅</span>
                Book Appointment
            </a>

            <a href="my_appointments.php">
                <span>📋</span>
                My Appointments
            </a>

            <a href="my_bills.php">
                <span>💰</span>
                My Bills
            </a>

            <a href="profile.php">
                <span>👤</span>
                My Profile
            </a>
        </nav>

        <div class="sidebar-bottom">
            <a href="logout.php" class="logout">
                <span>🚪</span>
                Logout
            </a>
        </div>

    </div>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- TOP BAR -->
        <div class="topbar">

            <div>
                <p class="small-title">Patient Portal</p>
                <h1>Dashboard</h1>
            </div>

            <div class="user-box">
                <div class="user-icon">👤</div>

                <div>
                    <strong>
                        <?php echo htmlspecialchars($fullname); ?>
                    </strong>

                    <small>Patient</small>
                </div>
            </div>

        </div>

        <!-- WELCOME BOX -->
        <div class="welcome-box">

            <div>
                <p class="welcome-small">Welcome back 👋</p>

                <h2>
                    Hello, <?php echo htmlspecialchars($fullname); ?>!
                </h2>

                <p>
                    Manage your appointments and personal information
                    from your patient dashboard.
                </p>
            </div>

            <div class="welcome-icon">🏥</div>

        </div>

        <!-- STAT CARDS -->
        <div class="stats">

            <div class="stat-card">

                <div class="stat-icon blue">📅</div>

                <div>
                    <p>Total Appointments</p>
                    <h2><?php echo $totalAppointments; ?></h2>
                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon orange">⏳</div>

                <div>
                    <p>Pending Appointments</p>
                    <h2><?php echo $pendingAppointments; ?></h2>
                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon green">👨‍⚕️</div>

                <div>
                    <p>Healthcare</p>
                    <h2>7 Heaven</h2>
                </div>

            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <h2 class="section-title">Quick Actions</h2>

        <div class="cards">

            <div class="card">

                <div class="card-icon blue-bg">📅</div>

                <h3>Book Appointment</h3>

                <p>
                    Schedule an appointment with one of our doctors.
                </p>

                <a href="book_appointment.php">Book Now →</a>

            </div>

            <div class="card">

                <div class="card-icon purple-bg">📋</div>

                <h3>My Appointments</h3>

                <p>
                    Check your upcoming and previous appointments.
                </p>

                <a href="my_appointments.php">
                    View Appointments →
                </a>

            </div>

            <div class="card">

                <div class="card-icon green-bg">👤</div>

                <h3>My Profile</h3>

                <p>
                    View and manage your personal information.
                </p>

                <a href="profile.php">View Profile →</a>

            </div>

        </div>

    </div>

</body>
</html>
```