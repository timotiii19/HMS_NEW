<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');
// Connect to the database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Check for successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Doctor Schedules
$query = "SELECT * FROM doctorschedule";
$result = $conn->query($query);
$doctorSchedules = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctorSchedules[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule - Nurse Dashboard</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Doctor Schedule</h2>

    <!-- Doctor Schedule Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Doctor ID</th>
                <th>Location ID</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctorSchedules as $schedule): ?>
                <tr>
                    <td><?= $schedule['DoctorID'] ?></td>
                    <td><?= $schedule['LocationID'] ?></td>
                    <td><?= $schedule['ScheduleDate'] ?></td>
                    <td><?= $schedule['StartTime'] ?></td>
                    <td><?= $schedule['EndTime'] ?></td>
                    <td><?= $schedule['Status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
