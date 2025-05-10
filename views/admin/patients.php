<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_header.php');
include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Fetch patients from the database
function getPatients($conn) {
    $query = "SELECT * FROM patients";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$patients = getPatients($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patients</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css"> <!-- Path to your CSS -->
</head>
<body>
<div class="content">
    <h2>View Patients</h2>

    <!-- Patients List -->
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Contact</th>
                <th>Sex</th>
                <th>Address</th>
                <th>Patient Type</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($patients) > 0): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= $patient['PatientID'] ?></td>
                        <td><?= $patient['Name'] ?></td>
                        <td><?= $patient['DateOfBirth'] ?></td>
                        <td><?= $patient['Contact'] ?></td>
                        <td><?= $patient['Sex'] ?></td>
                        <td><?= $patient['Address'] ?></td>
                        <td>
                            <!-- Since this is just a view, no links to edit or add patients -->
                            <a href="inpatients/create.php?patient_id=<?= $patient['PatientID'] ?>" class="btn btn-sm btn-primary">Inpatient</a>
                            <a href="outpatients/create.php?patient_id=<?= $patient['PatientID'] ?>" class="btn btn-sm btn-success">Outpatient</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No patients found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <br>
    <!-- Back Button -->
    <a href="index.php" class="btn">← Back to Dashboard</a>
</div>

</body>
</html>
