<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');

// Database connection
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Get patient details (view and update)
$patients = $conn->query("SELECT * FROM patients");

if (isset($_POST['update_patient'])) {
    // Example: Update patient data (e.g., vitals)
    $patient_id = $_POST['patient_id'];
    $vital_signs = $_POST['vital_signs'];
    $stmt = $conn->prepare("UPDATE patients SET VitalSigns = ? WHERE PatientID = ?");
    $stmt->bind_param("si", $vital_signs, $patient_id);
    $stmt->execute();
    header("Location: patient.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Patient Management</h2>

    <!-- Patient List -->
    <table border="1">
        <tr>
            <th>Patient ID</th>
            <th>Name</th>
            <th>Vital Signs</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $patients->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['PatientID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td>
                <form method="POST">
                    <!-- Check if VitalSigns is empty or NULL, and display a default message if so -->
                    <input type="text" name="vital_signs" value="<?= isset($row['VitalSigns']) ? $row['VitalSigns'] : 'Not Available' ?>" required>
                    <input type="hidden" name="patient_id" value="<?= $row['PatientID'] ?>">
                    <button type="submit" name="update_patient">Update</button>
                </form>
            </td>
            <td><a href="view_patient.php?id=<?= $row['PatientID'] ?>">View Details</a></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
