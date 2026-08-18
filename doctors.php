<?php

include("config.php");

/* Get all doctors */
$result = mysqli_query($conn, "SELECT * FROM doctors ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>7 Heaven Hospital | Doctors</title>

    <!-- NAVBAR CSS -->
    <link rel="stylesheet" href="css/navbar.css">

    <!-- DOCTORS CSS -->
    <link rel="stylesheet" href="css/doctors.css?v=2">

</head>

<body>


<?php include("navbar.php"); ?>


<!-- ================================
     DOCTORS HERO
================================ -->

<section class="doctors-hero">

    <div class="hero-content">

        <span class="hero-label">
            OUR MEDICAL TEAM
        </span>

        <h1>
            Meet Our Expert Doctors
        </h1>

        <p>
            Our experienced healthcare professionals are committed
            to providing quality medical care for you and your family.
        </p>

    </div>

</section>



<!-- ================================
     DOCTORS
================================ -->

<section class="doctors-section">

    <div class="section-title">

        <span>MEDICAL PROFESSIONALS</span>

        <h2>Our Doctors</h2>

        <p>
            Choose the right doctor for your healthcare needs.
        </p>

    </div>


    <div class="doctor-grid">

        <?php

        if ($result && mysqli_num_rows($result) > 0) {

            while ($doctor = mysqli_fetch_assoc($result)) {

                /* Doctor Name */

                if (!empty($doctor['name'])) {

                    $doctorName = $doctor['name'];

                } elseif (!empty($doctor['doctor_name'])) {

                    $doctorName = $doctor['doctor_name'];

                } elseif (!empty($doctor['fullname'])) {

                    $doctorName = $doctor['fullname'];

                } elseif (!empty($doctor['full_name'])) {

                    $doctorName = $doctor['full_name'];

                } else {

                    $doctorName = "Doctor";

                }


                /* Specialization */

                if (!empty($doctor['specialization'])) {

                    $specialization = $doctor['specialization'];

                } else {

                    $specialization = "Medical Specialist";

                }


                /* Phone */

                $phone = "";

                if (!empty($doctor['phone'])) {

                    $phone = $doctor['phone'];

                }

        ?>


        <div class="doctor-card">

            <div class="doctor-avatar">
                👨‍⚕️
            </div>


            <h3>
                Dr. <?php echo htmlspecialchars($doctorName); ?>
            </h3>


            <p class="specialization">
                <?php echo htmlspecialchars($specialization); ?>
            </p>


            <?php if ($phone != "") { ?>

                <p class="doctor-info">
                    📞 <?php echo htmlspecialchars($phone); ?>
                </p>

            <?php } ?>


            <a
                href="login.php"
                class="appointment-btn"
            >
                📅 Book Appointment
            </a>

        </div>


        <?php

            }

        } else {

        ?>

            <div class="no-doctors">

                <div class="no-doctor-icon">
                    👨‍⚕️
                </div>

                <h3>
                    No Doctors Available
                </h3>

                <p>
                    Doctor information will appear here soon.
                </p>

            </div>

        <?php } ?>

    </div>

</section>



<!-- ================================
     CTA
================================ -->

<section class="doctor-cta">

    <div>

        <span>NEED MEDICAL ASSISTANCE?</span>

        <h2>
            Your Health Deserves The Best Care
        </h2>

        <p>
            Book an appointment with one of our healthcare professionals.
        </p>

    </div>


    <a href="login.php">
        Book Appointment →
    </a>

</section>



<!-- ================================
     FOOTER
================================ -->

<footer class="footer">

    <strong>
        🏥 7 Heaven Hospital
    </strong>

    <p>
        © 2026 7 Heaven Hospital Management System.
        All Rights Reserved.
    </p>

</footer>


</body>
</html>