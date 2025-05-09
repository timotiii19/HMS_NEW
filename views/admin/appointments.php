<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');

// Connect database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Handle Add Appointment
if (isset($_POST['add_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION['doctor_id']; // Assuming the doctor's ID is stored in session
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, AppointmentTime, Reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Handle Edit Appointment
if (isset($_POST['edit_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("UPDATE appointments SET PatientID = ?, AppointmentDate = ?, AppointmentTime = ?, Reason = ? WHERE AppointmentID = ?");
    $stmt->bind_param("isssi", $patient_id, $appointment_date, $appointment_time, $reason, $appointment_id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Fetch appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE DoctorID = ".$_SESSION['doctor_id']);

// Fetch patients
$patients = $conn->query("SELECT PatientID, Name FROM patient");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Appointments</h2>

    <!-- Add Appointment Form -->
    <h3>Add Appointment</h3>
    <form method="POST">
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php while ($p = $patients->fetch_assoc()) { ?>
                <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
            <?php } ?>
        </select>
        <input type="date" name="appointment_date" required>
        <input type="time" name="appointment_time" required>
        <textarea name="reason" placeholder="Reason for appointment" required></textarea>
        <button type="submit" name="add_appointment">Add Appointment</button>
    </form>

    <h3>Existing Appointments</h3>
    <table border="1">
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $appointments->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['PatientID']; ?></td>
                <td><?php echo $row['AppointmentDate']; ?></td>
                <td><?php echo $row['AppointmentTime']; ?></td>
                <td><?php echo $row['Reason']; ?></td>
                <td>
                    <!-- Edit Appointment Form -->
                    <button onclick="openEditForm(<?php echo $row['AppointmentID']; ?>, <?php echo $row['PatientID']; ?>, '<?php echo $row['AppointmentDate']; ?>', '<?php echo $row['AppointmentTime']; ?>', '<?php echo $row['Reason']; ?>')">Edit</button>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Edit Appointment Modal -->
    <div id="editModal" style="display:none;">
        <h3>Edit Appointment</h3>
        <form method="POST">
            <input type="hidden" name="appointment_id" id="appointment_id">
            <select name="patient_id" id="edit_patient_id" required>
                <option value="">Select Patient</option>
                <?php while ($p = $patients->fetch_assoc()) { ?>
                    <option value="<?php echo $p['PatientID']; ?>"><?php echo $p['Name']; ?></option>
                <?php } ?>
            </select>
            <input type="date" name="appointment_date" id="edit_appointment_date" required>
            <input type="time" name="appointment_time" id="edit_appointment_time" required>
            <textarea name="reason" id="edit_reason" required></textarea>
            <button type="submit" name="edit_appointment">Save Changes</button>
            <button type="button" onclick="closeEditForm()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openEditForm(appointment_id, patient_id, appointment_date, appointment_time, reason) {
        document.getElementById('appointment_id').value = appointment_id;
        document.getElementById('edit_patient_id').value = patient_id;
        document.getElementById('edit_appointment_date').value = appointment_date;
        document.getElementById('edit_appointment_time').value = appointment_time;
        document.getElementById('edit_reason').value = reason;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>
