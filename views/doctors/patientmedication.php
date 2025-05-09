<?php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch medications for the logged-in doctor
$doctor_id = $_SESSION['doctor_id']; // Assuming the doctor's ID is stored in session
$query = "SELECT * FROM patientmedication WHERE DoctorID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$medications_result = $stmt->get_result();

// Fetch the medications from the result
$medications = [];
while ($row = $medications_result->fetch_assoc()) {
    $medications[] = (object) $row;
}

// Handle Add Medication Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['PatientID'];
    $medicine_id = $_POST['MedicineID'];
    $doctor_id = $_SESSION['doctor_id']; // Assuming doctor's ID is stored in session
    $dosage = $_POST['Dosage'];
    $frequency = $_POST['Frequency'];
    $start_date = $_POST['StartDate'];
    $end_date = $_POST['EndDate'];

    // Insert the new medication into the database
    $stmt = $conn->prepare("INSERT INTO patient_medication (PatientID, MedicineID, DoctorID, Dosage, Frequency, StartDate, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissss", $patient_id, $medicine_id, $doctor_id, $dosage, $frequency, $start_date, $end_date);
    $stmt->execute();
    header("Location: patientmedication.php"); // Refresh page after submission
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Medications</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Patient Medications</h2>

    <!-- Add Form -->
    <form method="POST" action="patientmedication.php" class="mb-4">
        <div class="row g-2">
            <div class="col"><input type="number" name="PatientID" class="form-control" placeholder="Patient ID" required></div>
            <div class="col"><input type="number" name="MedicineID" class="form-control" placeholder="Medicine ID" required></div>
            <div class="col"><input type="number" name="DoctorID" class="form-control" placeholder="Doctor ID" value="<?= $_SESSION['doctor_id'] ?>" readonly></div>
            <div class="col"><input type="text" name="Dosage" class="form-control" placeholder="Dosage" required></div>
            <div class="col"><input type="text" name="Frequency" class="form-control" placeholder="Frequency" required></div>
            <div class="col"><input type="date" name="StartDate" class="form-control" required></div>
            <div class="col"><input type="date" name="EndDate" class="form-control" required></div>
            <div class="col"><button class="btn btn-primary" type="submit">Add</button></div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Patient</th><th>Medicine</th><th>Doctor</th><th>Dosage</th><th>Frequency</th><th>Start</th><th>End</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($medications as $m): ?>
                <tr>
                    <td><?= $m->PatientMedicationID ?></td>
                    <td><?= $m->PatientID ?></td>
                    <td><?= $m->MedicineID ?></td>
                    <td><?= $m->DoctorID ?></td>
                    <td><?= $m->Dosage ?></td>
                    <td><?= $m->Frequency ?></td>
                    <td><?= $m->StartDate ?></td>
                    <td><?= $m->EndDate ?></td>
                    <td><a href="/patientmedication/edit/<?= $m->PatientMedicationID ?>" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form -->
    <?php if (isset($editMedication)): ?>
        <hr>
        <h4>Edit Medication</h4>
        <form method="POST" action="/patientmedication/update/<?= $editMedication->PatientMedicationID ?>">
            <div class="row g-2">
                <div class="col"><input type="number" name="PatientID" class="form-control" value="<?= $editMedication->PatientID ?>"></div>
                <div class="col"><input type="number" name="MedicineID" class="form-control" value="<?= $editMedication->MedicineID ?>"></div>
                <div class="col"><input type="number" name="DoctorID" class="form-control" value="<?= $editMedication->DoctorID ?>"></div>
                <div class="col"><input type="text" name="Dosage" class="form-control" value="<?= $editMedication->Dosage ?>"></div>
                <div class="col"><input type="text" name="Frequency" class="form-control" value="<?= $editMedication->Frequency ?>"></div>
                <div class="col"><input type="date" name="StartDate" class="form-control" value="<?= $editMedication->StartDate ?>"></div>
                <div class="col"><input type="date" name="EndDate" class="form-control" value="<?= $editMedication->EndDate ?>"></div>
                <div class="col"><button class="btn btn-success" type="submit">Update</button></div>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
