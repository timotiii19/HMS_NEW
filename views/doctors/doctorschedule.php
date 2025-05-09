<?php
session_start();


include('../../includes/doctor_sidebar.php');

// Database connection
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Fetch doctor schedules (You may need to modify this based on your database structure)
$query = "SELECT * FROM doctorschedule WHERE DoctorID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['DoctorID']); // Assuming you store the DoctorID in the session
$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC); // Fetch the result as an associative array

// Check if there's a request to edit a schedule
$editSchedule = null;
if (isset($_GET['edit'])) {
    $scheduleId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM doctorschedule WHERE DoctorScheduleID = ?");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editSchedule = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedules</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Doctor Schedules</h2>

    <!-- Add New Schedule Form -->
    <form method="POST" action="/doctorschedule/store" class="mb-4">
        <div class="row g-2">
            <div class="col"><input type="number" name="DoctorID" class="form-control" placeholder="Doctor ID" required></div>
            <div class="col"><input type="number" name="LocationID" class="form-control" placeholder="Location ID" required></div>
            <div class="col"><input type="date" name="ScheduleDate" class="form-control" required></div>
            <div class="col"><input type="time" name="StartTime" class="form-control" required></div>
            <div class="col"><input type="time" name="EndTime" class="form-control" required></div>
            <div class="col">
                <select name="Status" class="form-control" required>
                    <option value="Regular">Regular</option>
                    <option value="Resident">Resident</option>
                </select>
            </div>
            <div class="col"><button class="btn btn-primary" type="submit">Add</button></div>
        </div>
    </form>

    <!-- Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Doctor ID</th><th>Location</th><th>Date</th><th>Start</th><th>End</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedules as $s): ?>
                <tr>
                    <td><?= $s['DoctorScheduleID'] ?></td>
                    <td><?= $s['DoctorID'] ?></td>
                    <td><?= $s['LocationID'] ?></td>
                    <td><?= $s['ScheduleDate'] ?></td>
                    <td><?= $s['StartTime'] ?></td>
                    <td><?= $s['EndTime'] ?></td>
                    <td><?= $s['Status'] ?></td>
                    <td>
                        <a href="/doctorschedule/edit/<?= $s['DoctorScheduleID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Form (if editing) -->
    <?php if (isset($editSchedule)): ?>
        <hr>
        <h4>Edit Schedule</h4>
        <form method="POST" action="/doctorschedule/update/<?= $editSchedule['DoctorScheduleID'] ?>">
            <div class="row g-2">
                <div class="col"><input type="number" name="DoctorID" class="form-control" value="<?= $editSchedule['DoctorID'] ?>" required></div>
                <div class="col"><input type="number" name="LocationID" class="form-control" value="<?= $editSchedule['LocationID'] ?>" required></div>
                <div class="col"><input type="date" name="ScheduleDate" class="form-control" value="<?= $editSchedule['ScheduleDate'] ?>" required></div>
                <div class="col"><input type="time" name="StartTime" class="form-control" value="<?= $editSchedule['StartTime'] ?>" required></div>
                <div class="col"><input type="time" name="EndTime" class="form-control" value="<?= $editSchedule['EndTime'] ?>" required></div>
                <div class="col">
                    <select name="Status" class="form-control" required>
                        <option value="Regular" <?= $editSchedule['Status'] == 'Regular' ? 'selected' : '' ?>>Regular</option>
                        <option value="Resident" <?= $editSchedule['Status'] == 'Resident' ? 'selected' : '' ?>>Resident</option>
                    </select>
                </div>
                <div class="col"><button class="btn btn-success" type="submit">Update</button></div>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
