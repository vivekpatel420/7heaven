<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$user_email = $_SESSION['user'];


/* ==============================
   GET PATIENT
============================== */

$user_email_safe = mysqli_real_escape_string(
    $conn,
    $user_email
);

$userQuery = mysqli_query(
    $conn,
    "SELECT id, fullname, email
     FROM users
     WHERE email='$user_email_safe'"
);

if (!$userQuery) {

    die(
        "User Query Error: " .
        mysqli_error($conn)
    );

}

$user = mysqli_fetch_assoc($userQuery);


if (!$user) {

    session_destroy();

    header("Location: ../login.php");

    exit();

}


$patient_id = $user['id'];

$fullname = $user['fullname'];

$email = $user['email'];


/* ==============================
   GET PATIENT BILLS
============================== */

$bills = mysqli_query(
    $conn,

    "SELECT

        billing.id,
        billing.amount,
        billing.payment_status,
        billing.payment_method,
        billing.bill_date,
        billing.description,

        appointments.appointment_date,
        appointments.appointment_time,

        doctors.fullname AS doctor_name,
        doctors.specialization

     FROM billing

     INNER JOIN appointments
        ON billing.appointment_id = appointments.id

     INNER JOIN doctors
        ON appointments.doctor_id = doctors.id

     WHERE billing.patient_id='$patient_id'

     ORDER BY billing.id DESC"
);


if (!$bills) {

    die(
        "Billing Query Error: " .
        mysqli_error($conn)
    );

}


/* ==============================
   BILL TOTALS
============================== */

$totalAmount = 0;

$paidAmount = 0;

$pendingAmount = 0;


$totalQuery = mysqli_query(
    $conn,

    "SELECT

        SUM(amount) AS total,

        SUM(
            CASE
                WHEN payment_status='Paid'
                THEN amount
                ELSE 0
            END
        ) AS paid,

        SUM(
            CASE
                WHEN payment_status='Pending'
                THEN amount
                ELSE 0
            END
        ) AS pending

     FROM billing

     WHERE patient_id='$patient_id'"
);


if ($totalQuery) {

    $totals = mysqli_fetch_assoc(
        $totalQuery
    );

    $totalAmount =
        $totals['total'] ?? 0;

    $paidAmount =
        $totals['paid'] ?? 0;

    $pendingAmount =
        $totals['pending'] ?? 0;
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
        My Bills | 7 Heaven Hospital
    </title>


    <!-- PATIENT DASHBOARD CSS -->

    <link
        rel="stylesheet"
        href="dashboard.css"
    >


    <!-- MY BILLS CSS -->

    <link
        rel="stylesheet"
        href="my_bills.css"
    >


</head>


<body>


<!-- ==================================
     PATIENT SIDEBAR
================================== -->

<div class="sidebar">


    <div class="logo">


        <div class="logo-icon">
            🏥
        </div>


        <h2>
            7 Heaven
        </h2>


        <p>
            Patient Portal
        </p>


    </div>



    <nav>


        <a href="dashboard.php">

            🏠 Dashboard

        </a>


        <a href="book_appointment.php">

            📅 Book Appointment

        </a>


        <a href="my_appointments.php">

            📋 My Appointments

        </a>


        <a
            href="my_bills.php"
            class="active"
        >

            💰 My Bills

        </a>


        <a href="profile.php">

            👤 My Profile

        </a>


    </nav>



    <div class="sidebar-bottom">


        <a
            href="logout.php"
            class="logout"
        >

            🚪 Logout

        </a>


    </div>


</div>



<!-- ==================================
     MAIN CONTENT
================================== -->

<div class="main">


    <div class="patient-billing-page">


        <!-- ==================================
             PAGE HEADER
        ================================== -->

        <div class="billing-header">


            <div>


                <span class="billing-label">

                    PATIENT PORTAL

                </span>


                <h1>

                    💰 My Bills

                </h1>


                <p>

                    View your hospital billing records
                    and payment information.

                </p>


            </div>



            <div class="patient-box">

                👤

                <span>

                    <?php
                    echo htmlspecialchars(
                        $fullname
                    );
                    ?>

                </span>

            </div>


        </div>



        <!-- ==================================
             BILL SUMMARY
        ================================== -->

        <div class="patient-billing-summary">


            <!-- TOTAL -->

            <div class="billing-summary-card">


                <div class="summary-icon">

                    💰

                </div>


                <p>

                    Total Bills

                </p>


                <h2>

                    ₹<?php

                    echo number_format(
                        $totalAmount,
                        2
                    );

                    ?>

                </h2>


            </div>



            <!-- PAID -->

            <div class="billing-summary-card">


                <div class="summary-icon paid-icon">

                    ✅

                </div>


                <p>

                    Paid Amount

                </p>


                <h2>

                    ₹<?php

                    echo number_format(
                        $paidAmount,
                        2
                    );

                    ?>

                </h2>


            </div>



            <!-- PENDING -->

            <div class="billing-summary-card">


                <div class="summary-icon pending-icon">

                    ⏳

                </div>


                <p>

                    Pending Amount

                </p>


                <h2>

                    ₹<?php

                    echo number_format(
                        $pendingAmount,
                        2
                    );

                    ?>

                </h2>


            </div>


        </div>



        <!-- ==================================
             BILLING HISTORY
        ================================== -->

        <div class="patient-bills-card">


            <div class="card-heading">


                <div>


                    <h2>

                        📋 Billing History

                    </h2>


                    <p>

                        Your hospital payment records.

                    </p>


                </div>


            </div>



            <?php

            if (
                mysqli_num_rows($bills) > 0
            ):

            ?>


                <div class="bills-list">


                    <?php


                    while (
                        $bill =
                        mysqli_fetch_assoc(
                            $bills
                        )
                    ) {


                        $status =
                            strtolower(
                                $bill[
                                    'payment_status'
                                ]
                            );


                    ?>


                        <!-- BILL ITEM -->

                        <div class="bill-item">


                            <!-- ICON -->

                            <div class="bill-icon">

                                🧾

                            </div>



                            <!-- DETAILS -->

                            <div class="bill-details">


                                <h3>

                                    Bill #

                                    <?php
                                    echo $bill['id'];
                                    ?>

                                </h3>



                                <p>


                                    👨‍⚕️ Dr.

                                    <?php
                                    echo htmlspecialchars(
                                        $bill[
                                            'doctor_name'
                                        ]
                                    );
                                    ?>


                                    <span>

                                        •

                                    </span>


                                    <?php
                                    echo htmlspecialchars(
                                        $bill[
                                            'specialization'
                                        ]
                                    );
                                    ?>


                                </p>



                                <p>


                                    📅


                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $bill[
                                                'appointment_date'
                                            ]
                                        )
                                    );

                                    ?>


                                    &nbsp;


                                    🕐


                                    <?php

                                    echo date(
                                        "h:i A",
                                        strtotime(
                                            $bill[
                                                'appointment_time'
                                            ]
                                        )
                                    );

                                    ?>


                                </p>



                                <?php

                                if (
                                    !empty(
                                        $bill[
                                            'description'
                                        ]
                                    )
                                ):

                                ?>


                                    <p
                                        class="description"
                                    >

                                        📝

                                        <?php
                                        echo htmlspecialchars(
                                            $bill[
                                                'description'
                                            ]
                                        );
                                        ?>

                                    </p>


                                <?php

                                endif;

                                ?>


                            </div>



                            <!-- PAYMENT -->

                            <div class="bill-payment">


                                <div class="bill-amount">

                                    ₹<?php

                                    echo number_format(
                                        $bill[
                                            'amount'
                                        ],
                                        2
                                    );

                                    ?>

                                </div>



                                <?php


                                if (
                                    $status ==
                                    "paid"
                                ) {


                                    echo '

                                    <span
                                        class="bill-status paid"
                                    >

                                        ✅ Paid

                                    </span>

                                    ';


                                }

                                elseif (
                                    $status ==
                                    "cancelled"
                                ) {


                                    echo '

                                    <span
                                        class="bill-status cancelled"
                                    >

                                        ❌ Cancelled

                                    </span>

                                    ';


                                }

                                else {


                                    echo '

                                    <span
                                        class="bill-status pending"
                                    >

                                        ⏳ Pending

                                    </span>

                                    ';

                                }


                                ?>


                                <small>

                                    <?php

                                    echo htmlspecialchars(
                                        $bill[
                                            'payment_method'
                                        ]
                                    );

                                    ?>

                                </small>


                            </div>


                        </div>


                    <?php

                    }

                    ?>


                </div>


            <?php

            else:

            ?>


                <!-- NO BILLS -->

                <div class="no-bills">


                    <div class="no-bills-icon">

                        💰

                    </div>


                    <h2>

                        No Bills Found

                    </h2>


                    <p>

                        You don't have any
                        billing records yet.

                    </p>


                </div>


            <?php

            endif;

            ?>


        </div>


    </div>


</div>


</body>

</html>