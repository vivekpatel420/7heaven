<?php

session_start();

include("config.php");

$message = "";


/* ==============================
   SEND CONTACT MESSAGE
============================== */

if (isset($_POST['send_message'])) {

    $name = mysqli_real_escape_string(
        $conn,
        $_POST['name']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $subject = mysqli_real_escape_string(
        $conn,
        $_POST['subject']
    );

    $msg = mysqli_real_escape_string(
        $conn,
        $_POST['message']
    );


    $sql = "INSERT INTO contact_messages
            (name, email, phone, subject, message)
            VALUES
            ('$name', '$email', '$phone', '$subject', '$msg')";


    if (mysqli_query($conn, $sql)) {

        $message = "success";

    } else {

        $message = "error";

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

    <title>
        Contact 7 Heaven Hospital
    </title>


    <!-- NAVBAR CSS -->

    <link
        rel="stylesheet"
        href="css/navbar.css"
    >


    <!-- CONTACT CSS -->

    <link
        rel="stylesheet"
        href="css/contact.css"
    >


    <!-- RESPONSIVE CSS -->

    <link
        rel="stylesheet"
        href="css/responsive.css"
    >

</head>


<body>


<!-- ==============================
     NAVBAR
============================== -->

<?php include("navbar.php"); ?>



<!-- ==============================
     HERO
============================== -->

<section class="contact-hero">

    <div class="hero-content">

        <span>
            GET IN TOUCH
        </span>

        <h1>
            Contact 7 Heaven
        </h1>

        <p>
            We're here to help you with your healthcare needs.
        </p>

    </div>

</section>



<!-- ==============================
     CONTACT SECTION
============================== -->

<section class="contact-section">


    <!-- INFORMATION -->

    <div class="contact-info">

        <span class="label">
            CONTACT INFORMATION
        </span>


        <h2>

            We're Always Here

            <span>
                For You
            </span>

        </h2>


        <p class="description">

            Have a question or need assistance?
            Contact our healthcare team and we'll be happy to help.

        </p>



        <!-- ADDRESS -->

        <div class="info-box">

            <div class="info-icon">
                📍
            </div>


            <div>

                <h3>
                    Hospital Address
                </h3>

                <p>

                    7 Heaven Hospital<br>

                    Main Road, City Center<br>

                    India

                </p>

            </div>

        </div>



        <!-- PHONE -->

        <div class="info-box">

            <div class="info-icon">
                📞
            </div>


            <div>

                <h3>
                    Phone
                </h3>

                <p>

                    +91 98765 43210<br>

                    Emergency: 108

                </p>

            </div>

        </div>



        <!-- EMAIL -->

        <div class="info-box">

            <div class="info-icon">
                ✉️
            </div>


            <div>

                <h3>
                    Email
                </h3>

                <p>

                    info@7heaven.com<br>

                    support@7heaven.com

                </p>

            </div>

        </div>



        <!-- WORKING HOURS -->

        <div class="info-box">

            <div class="info-icon">
                🕒
            </div>


            <div>

                <h3>
                    Working Hours
                </h3>

                <p>

                    Monday - Saturday<br>

                    9:00 AM - 8:00 PM

                </p>

            </div>

        </div>


    </div>



    <!-- ==============================
         CONTACT FORM
    ============================== -->

    <div class="contact-form">


        <h2>
            Send Us a Message
        </h2>


        <p>
            Fill out the form below and our team will contact you.
        </p>



        <!-- SUCCESS MESSAGE -->

        <?php if ($message == "success") { ?>

            <div class="success">

                ✅ Your message has been sent successfully!

            </div>

        <?php } ?>



        <!-- ERROR MESSAGE -->

        <?php if ($message == "error") { ?>

            <div class="error">

                ❌ Something went wrong. Please try again.

            </div>

        <?php } ?>



        <!-- FORM -->

        <form method="POST">


            <!-- NAME + EMAIL -->

            <div class="form-row">


                <div class="input-group">

                    <label>
                        Full Name
                    </label>


                    <input
                        type="text"
                        name="name"
                        placeholder="Enter your name"
                        required
                    >

                </div>



                <div class="input-group">

                    <label>
                        Email
                    </label>


                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


            </div>



            <!-- PHONE + SUBJECT -->

            <div class="form-row">


                <div class="input-group">

                    <label>
                        Phone
                    </label>


                    <input
                        type="text"
                        name="phone"
                        placeholder="Enter phone number"
                        required
                    >

                </div>



                <div class="input-group">

                    <label>
                        Subject
                    </label>


                    <input
                        type="text"
                        name="subject"
                        placeholder="Message subject"
                        required
                    >

                </div>


            </div>



            <!-- MESSAGE -->

            <div class="input-group">

                <label>
                    Message
                </label>


                <textarea
                    name="message"
                    rows="6"
                    placeholder="Write your message..."
                    required
                ></textarea>

            </div>



            <!-- BUTTON -->

            <button
                type="submit"
                name="send_message"
            >

                Send Message →

            </button>


        </form>


    </div>


</section>



<!-- ==============================
     APPOINTMENT CTA
============================== -->

<section class="contact-cta">


    <div>

        <h2>
            Need an Appointment?
        </h2>


        <p>

            Login to your patient account and book an appointment.

        </p>

    </div>


    <a href="login.php">

        Book Appointment

    </a>


</section>



<!-- ==============================
     FOOTER
============================== -->

<footer>


    <strong>
        🏥 7 Heaven Hospital
    </strong>


    <p>

        © 2026 7 Heaven Hospital Management System

    </p>


</footer>



</body>

</html>