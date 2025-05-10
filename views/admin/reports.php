<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Billing Summary Report
$billing = $conn->query("SELECT * FROM patientbilling");

// Doctor List Report
$reports = $conn->query("
    SELECT doctor.*, dept.DepartmentName
    FROM doctor
    LEFT JOIN department dept ON doctor.DepartmentID = dept.DepartmentID
");

// Nurses List Report
$nurses = $conn->query("SELECT * FROM nurse");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Reports</h2>

    <!-- Billing Summary -->
    <h3>Billing Summary</h3>

    <!-- Export Button -->
    <form method="post" action="export_billing_pdf.php">
        <button type="submit" class="button">Export Billing to PDF</button>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient Name</th>
            <th>Doctor Fee</th>
            <th>Medicine Cost</th>
            <th>Total Amount</th>
        </tr>
        <?php if ($billing->num_rows > 0) {
            while ($row = $billing->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= htmlspecialchars($row['doctor_fee']) ?></td>
                    <td><?= htmlspecialchars($row['medicine_cost']) ?></td>
                    <td><?= htmlspecialchars($row['total_amount']) ?></td>
                </tr>
            <?php }
        } else {
            echo "<tr><td colspan='5'>No billing data available.</td></tr>";
        } ?>
    </table>

    <!-- Doctor List -->
    <h3>Doctors List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialty</th>
            <th>Department</th>
        </tr>
        <?php if ($reports->num_rows > 0) {
            while ($row = $reports->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['DoctorID'] ?></td>
                    <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorType']) ?></td>
                    <td><?= $row['DepartmentName'] ?? 'Not Assigned' ?></td>
                </tr>
            <?php }
        } else {
            echo "<tr><td colspan='4'>No doctors available.</td></tr>";
        } ?>
    </table>

    <!-- Nurses List -->
    <h3>Nurses List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nurse Name</th>
            <th>Email</th>
        </tr>
        <?php if ($nurses->num_rows > 0) {
            while ($row = $nurses->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['NurseID'] ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                </tr>
            <?php }
        } else {
            echo "<tr><td colspan='3'>No nurses available.</td></tr>";
        } ?>
    </table>

</div>

</body>
</html>
