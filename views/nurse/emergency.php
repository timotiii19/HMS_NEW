<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');

// Connect to database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Insert emergency entry
if (isset($_POST['log_emergency'])) {
    $patient_id = $_POST['patient_id'];
    $symptoms = $_POST['symptoms'];
    $urgency = $_POST['urgency'];

    $stmt = $conn->prepare("INSERT INTO emergency (PatientID, Symptoms, UrgencyLevel) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $patient_id, $symptoms, $urgency);
    $stmt->execute();
    header("Location: emergency.php");
    exit();
}

// Get all patients for dropdown
$patients = $conn->query("SELECT PatientID, Name FROM patients");

// Get emergency logs
$emergencies = $conn->query("
    SELECT e.*, p.Name AS PatientName 
    FROM emergency e
    JOIN patients p ON e.PatientID = p.PatientID
    ORDER BY e.LoggedAt DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Emergency Log</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Emergency Log</h2>

    <form method="post">
        <label>Patient:</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php while ($p = $patients->fetch_assoc()) {
                echo "<option value='".$p['PatientID']."'>".$p['Name']."</option>";
            } ?>
        </select>

        <label>Symptoms:</label>
        <textarea name="symptoms" required></textarea>

        <label>Urgency Level:</label>
        <select name="urgency" required>
            <option value="">Select Urgency</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>

        <button type="submit" name="log_emergency">Log Emergency</button>
    </form>

    <h3>Recent Emergency Records</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Symptoms</th>
            <th>Urgency</th>
            <th>Logged At</th>
        </tr>
        <?php while ($row = $emergencies->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['EmergencyID'] ?></td>
            <td><?= $row['PatientName'] ?></td>
            <td><?= $row['Symptoms'] ?></td>
            <td><?= $row['UrgencyLevel'] ?></td>
            <td><?= $row['LoggedAt'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
