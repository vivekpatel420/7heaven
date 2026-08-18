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
   ADD BILL
========================================= */

if (isset($_POST['add_bill'])) {

    $patient_id = intval($_POST['patient_id'] ?? 0);
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $payment_status = $_POST['payment_status'] ?? 'Pending';
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $bill_date = $_POST['bill_date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');

    $allowed_status = ['Pending', 'Paid', 'Cancelled'];
    $allowed_methods = ['Cash', 'UPI', 'Card', 'Online'];

    if (!in_array($payment_status, $allowed_status)) {
        $payment_status = 'Pending';
    }

    if (!in_array($payment_method, $allowed_methods)) {
        $payment_method = 'Cash';
    }

    if ($patient_id <= 0 || $appointment_id <= 0) {

        $message = "Please select a valid patient and appointment.";
        $message_type = "error";

    } elseif ($amount <= 0) {

        $message = "Please enter a valid billing amount.";
        $message_type = "error";

    } else {

        /* Check appointment belongs to patient */

        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM appointments
             WHERE id = ?
             AND patient_id = ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "ii",
            $appointment_id,
            $patient_id
        );

        mysqli_stmt_execute($check);

        $checkResult = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkResult) == 0) {

            $message = "Invalid appointment selected.";
            $message_type = "error";

        } else {

            /* Check duplicate bill */

            $duplicate = mysqli_prepare(
                $conn,
                "SELECT id
                 FROM billing
                 WHERE appointment_id = ?"
            );

            mysqli_stmt_bind_param(
                $duplicate,
                "i",
                $appointment_id
            );

            mysqli_stmt_execute($duplicate);

            $duplicateResult = mysqli_stmt_get_result($duplicate);

            if (mysqli_num_rows($duplicateResult) > 0) {

                $message =
                    "A bill already exists for this appointment.";

                $message_type = "error";

            } else {

                /* Create bill */

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO billing
                    (
                        patient_id,
                        appointment_id,
                        amount,
                        payment_status,
                        payment_method,
                        bill_date,
                        description
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "iidssss",
                    $patient_id,
                    $appointment_id,
                    $amount,
                    $payment_status,
                    $payment_method,
                    $bill_date,
                    $description
                );

                if (mysqli_stmt_execute($stmt)) {

                    $message = "Bill created successfully.";
                    $message_type = "success";

                } else {

                    $message =
                        "Billing Error: " .
                        mysqli_error($conn);

                    $message_type = "error";
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_stmt_close($duplicate);
        }

        mysqli_stmt_close($check);
    }
}


/* =========================================
   UPDATE PAYMENT STATUS
========================================= */

if (isset($_POST['update_payment'])) {

    $bill_id = intval($_POST['bill_id'] ?? 0);
    $payment_status = $_POST['payment_status'] ?? 'Pending';

    $allowed_status = [
        'Pending',
        'Paid',
        'Cancelled'
    ];

    if (in_array($payment_status, $allowed_status)) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE billing
             SET payment_status = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $payment_status,
            $bill_id
        );

        if (mysqli_stmt_execute($stmt)) {

            $message =
                "Payment status updated successfully.";

            $message_type = "success";

        } else {

            $message =
                "Unable to update payment status.";

            $message_type = "error";
        }

        mysqli_stmt_close($stmt);
    }
}


/* =========================================
   DELETE BILL
========================================= */

if (isset($_GET['delete'])) {

    $bill_id = intval($_GET['delete']);

    if ($bill_id > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM billing
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $bill_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: billing.php");
    exit();
}


/* =========================================
   GET PATIENTS
========================================= */

$patients = mysqli_query(
    $conn,
    "SELECT id, fullname, email
     FROM users
     ORDER BY fullname ASC"
);

if (!$patients) {
    die("Patient Query Error: " . mysqli_error($conn));
}


/* =========================================
   GET APPOINTMENTS
========================================= */

$appointments = mysqli_query(
    $conn,
    "SELECT
        appointments.id,
        appointments.patient_id,
        appointments.appointment_date,
        appointments.appointment_time,
        users.fullname AS patient_name,
        doctors.fullname AS doctor_name

     FROM appointments

     INNER JOIN users
        ON appointments.patient_id = users.id

     INNER JOIN doctors
        ON appointments.doctor_id = doctors.id

     WHERE appointments.status != 'Cancelled'

     ORDER BY
        appointments.appointment_date DESC"
);

if (!$appointments) {
    die(
        "Appointment Query Error: " .
        mysqli_error($conn)
    );
}


/* =========================================
   GET BILLS
========================================= */

$bills = mysqli_query(
    $conn,

    "SELECT
        billing.*,
        users.fullname AS patient_name,
        users.email AS patient_email,
        doctors.fullname AS doctor_name,
        appointments.appointment_date,
        appointments.appointment_time

     FROM billing

     INNER JOIN users
        ON billing.patient_id = users.id

     INNER JOIN appointments
        ON billing.appointment_id = appointments.id

     INNER JOIN doctors
        ON appointments.doctor_id = doctors.id

     ORDER BY billing.id DESC"
);

if (!$bills) {
    die(
        "Billing Query Error: " .
        mysqli_error($conn)
    );
}


/* =========================================
   BILLING TOTALS
========================================= */

$totalBill = 0;
$paidBill = 0;
$pendingBill = 0;

$totalQuery = mysqli_query(
    $conn,

    "SELECT
        SUM(amount) AS total,

        SUM(
            CASE
                WHEN payment_status = 'Paid'
                THEN amount
                ELSE 0
            END
        ) AS paid,

        SUM(
            CASE
                WHEN payment_status = 'Pending'
                THEN amount
                ELSE 0
            END
        ) AS pending

     FROM billing"
);

if ($totalQuery) {

    $totals = mysqli_fetch_assoc($totalQuery);

    $totalBill = $totals['total'] ?? 0;
    $paidBill = $totals['paid'] ?? 0;
    $pendingBill = $totals['pending'] ?? 0;
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
        Billing Management | 7 Heaven Hospital
    </title>

    <link
        rel="stylesheet"
        href="../css/dashboard.css"
    >

    <link
        rel="stylesheet"
        href="../css/billing.css"
    >

</head>

<body>


<div class="container">


    <!-- SIDEBAR -->

    <?php include("sidebar.php"); ?>


    <!-- CONTENT -->

    <div class="content">


        <div class="billing-page">


            <!-- HEADER -->

            <div class="page-header">

                <div>

                    <h1>
                        💰 Billing Management
                    </h1>

                    <p>
                        Create and manage patient bills
                        and payment status.
                    </p>

                </div>

            </div>


            <!-- MESSAGE -->

            <?php if ($message != "") { ?>

                <div
                    class="message
                    <?php echo $message_type; ?>"
                >

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php } ?>


            <!-- SUMMARY -->

            <div class="billing-summary">


                <div class="summary-card">

                    <div class="summary-icon">
                        💰
                    </div>

                    <p>
                        Total Billing
                    </p>

                    <h2>
                        ₹<?php
                        echo number_format(
                            $totalBill,
                            2
                        );
                        ?>
                    </h2>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        ✅
                    </div>

                    <p>
                        Paid Amount
                    </p>

                    <h2>
                        ₹<?php
                        echo number_format(
                            $paidBill,
                            2
                        );
                        ?>
                    </h2>

                </div>


                <div class="summary-card">

                    <div class="summary-icon">
                        ⏳
                    </div>

                    <p>
                        Pending Amount
                    </p>

                    <h2>
                        ₹<?php
                        echo number_format(
                            $pendingBill,
                            2
                        );
                        ?>
                    </h2>

                </div>


            </div>


            <!-- CREATE BILL -->

            <div class="billing-form-card">

                <h2>
                    ➕ Create New Bill
                </h2>


                <form method="POST">


                    <div class="form-grid">


                        <!-- PATIENT -->

                        <div class="form-group">

                            <label>
                                Patient
                            </label>

                            <select
                                name="patient_id"
                                required
                            >

                                <option value="">
                                    Select Patient
                                </option>

                                <?php

                                while (
                                    $patient =
                                    mysqli_fetch_assoc(
                                        $patients
                                    )
                                ) {

                                ?>

                                    <option
                                        value="<?php
                                        echo $patient['id'];
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $patient['fullname']
                                        );
                                        ?>

                                        -

                                        <?php
                                        echo htmlspecialchars(
                                            $patient['email']
                                        );
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- APPOINTMENT -->

                        <div class="form-group">

                            <label>
                                Appointment
                            </label>

                            <select
                                name="appointment_id"
                                required
                            >

                                <option value="">
                                    Select Appointment
                                </option>

                                <?php

                                while (
                                    $appointment =
                                    mysqli_fetch_assoc(
                                        $appointments
                                    )
                                ) {

                                ?>

                                    <option
                                        value="<?php
                                        echo $appointment['id'];
                                        ?>"
                                    >

                                        #<?php
                                        echo $appointment['id'];
                                        ?>

                                        -

                                        <?php
                                        echo htmlspecialchars(
                                            $appointment['patient_name']
                                        );
                                        ?>

                                        -

                                        Dr.
                                        <?php
                                        echo htmlspecialchars(
                                            $appointment['doctor_name']
                                        );
                                        ?>

                                        -

                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $appointment[
                                                    'appointment_date'
                                                ]
                                            )
                                        );
                                        ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <!-- AMOUNT -->

                        <div class="form-group">

                            <label>
                                Amount (₹)
                            </label>

                            <input
                                type="number"
                                name="amount"
                                min="0"
                                step="0.01"
                                placeholder="Enter amount"
                                required
                            >

                        </div>


                        <!-- PAYMENT STATUS -->

                        <div class="form-group">

                            <label>
                                Payment Status
                            </label>

                            <select
                                name="payment_status"
                            >

                                <option value="Pending">
                                    Pending
                                </option>

                                <option value="Paid">
                                    Paid
                                </option>

                                <option value="Cancelled">
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <!-- PAYMENT METHOD -->

                        <div class="form-group">

                            <label>
                                Payment Method
                            </label>

                            <select
                                name="payment_method"
                            >

                                <option value="Cash">
                                    Cash
                                </option>

                                <option value="UPI">
                                    UPI
                                </option>

                                <option value="Card">
                                    Card
                                </option>

                                <option value="Online">
                                    Online
                                </option>

                            </select>

                        </div>


                        <!-- BILL DATE -->

                        <div class="form-group">

                            <label>
                                Bill Date
                            </label>

                            <input
                                type="date"
                                name="bill_date"
                                value="<?php
                                echo date('Y-m-d');
                                ?>"
                                required
                            >

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="form-group full">

                            <label>
                                Description
                            </label>

                            <textarea
                                name="description"
                                placeholder="Consultation, medicine, laboratory test, etc."
                            ></textarea>

                        </div>


                    </div>


                    <button
                        type="submit"
                        name="add_bill"
                        class="save-btn"
                    >

                        💾 Create Bill

                    </button>


                </form>

            </div>


            <!-- BILLING RECORDS -->

            <div class="billing-table-card">


                <h2>
                    📋 Billing Records
                </h2>


                <div class="table-wrapper">


                    <?php if (
                        mysqli_num_rows($bills) > 0
                    ) { ?>


                        <table class="billing-table">


                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>Patient</th>

                                    <th>Doctor</th>

                                    <th>Appointment</th>

                                    <th>Amount</th>

                                    <th>Payment</th>

                                    <th>Bill Date</th>

                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php

                            $count = 1;

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


                                <tr>


                                    <td>

                                        <?php
                                        echo $count++;
                                        ?>

                                    </td>


                                    <td>

                                        <span
                                            class="patient-name"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $bill[
                                                    'patient_name'
                                                ]
                                            );
                                            ?>

                                        </span>

                                        <span
                                            class="details"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $bill[
                                                    'patient_email'
                                                ]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="doctor-name"
                                        >

                                            Dr.

                                            <?php
                                            echo htmlspecialchars(
                                                $bill[
                                                    'doctor_name'
                                                ]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

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

                                        <span
                                            class="details"
                                        >

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

                                        </span>

                                    </td>


                                    <td>

                                        <strong>
                                            ₹<?php
                                            echo number_format(
                                                $bill[
                                                    'amount'
                                                ],
                                                2
                                            );
                                            ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <?php

                                        if (
                                            $status ==
                                            "paid"
                                        ) {

                                            echo '
                                            <span
                                                class="status
                                                status-paid"
                                            >
                                                ✅ Paid
                                            </span>';

                                        } elseif (
                                            $status ==
                                            "cancelled"
                                        ) {

                                            echo '
                                            <span
                                                class="status
                                                status-cancelled"
                                            >
                                                ❌ Cancelled
                                            </span>';

                                        } else {

                                            echo '
                                            <span
                                                class="status
                                                status-pending"
                                            >
                                                ⏳ Pending
                                            </span>';

                                        }

                                        ?>

                                    </td>


                                    <td>

                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $bill[
                                                    'bill_date'
                                                ]
                                            )
                                        );
                                        ?>

                                    </td>


                                    <td>


                                        <form
                                            method="POST"
                                            class="status-form"
                                        >

                                            <input
                                                type="hidden"
                                                name="bill_id"
                                                value="<?php
                                                echo $bill['id'];
                                                ?>"
                                            >


                                            <select
                                                name="payment_status"
                                            >

                                                <option
                                                    value="Pending"
                                                    <?php
                                                    if (
                                                        $bill[
                                                            'payment_status'
                                                        ] ==
                                                        "Pending"
                                                    ) {
                                                        echo "selected";
                                                    }
                                                    ?>
                                                >
                                                    Pending
                                                </option>


                                                <option
                                                    value="Paid"
                                                    <?php
                                                    if (
                                                        $bill[
                                                            'payment_status'
                                                        ] ==
                                                        "Paid"
                                                    ) {
                                                        echo "selected";
                                                    }
                                                    ?>
                                                >
                                                    Paid
                                                </option>


                                                <option
                                                    value="Cancelled"
                                                    <?php
                                                    if (
                                                        $bill[
                                                            'payment_status'
                                                        ] ==
                                                        "Cancelled"
                                                    ) {
                                                        echo "selected";
                                                    }
                                                    ?>
                                                >
                                                    Cancelled
                                                </option>

                                            </select>


                                            <button
                                                type="submit"
                                                name="update_payment"
                                                class="update-btn"
                                            >

                                                Update

                                            </button>

                                        </form>


                                        <a
                                            href="billing.php?delete=<?php
                                            echo $bill['id'];
                                            ?>"
                                            class="delete-btn"
                                            onclick="return confirm(
                                                'Are you sure you want to delete this bill?'
                                            );"
                                        >

                                            🗑️ Delete

                                        </a>


                                    </td>


                                </tr>


                            <?php } ?>


                            </tbody>

                        </table>


                    <?php } else { ?>


                        <div class="empty-billing">

                            <div class="empty-icon">
                                💰
                            </div>

                            <h3>
                                No Billing Records
                            </h3>

                            <p>
                                Create a bill for a patient
                                appointment above.
                            </p>

                        </div>


                    <?php } ?>


                </div>

            </div>


        </div>

    </div>

</div>


</body>
</html>