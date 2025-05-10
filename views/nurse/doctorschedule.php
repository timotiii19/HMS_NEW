<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_header.php');
include('../../includes/nurse_sidebar.php');
include('../../config/db.php');

// Fetch doctor schedules or other content for the page
$query = "SELECT * FROM doctorschedule";
$result = $conn->query($query);
$schedules = $result->fetch_all(MYSQLI_ASSOC); // Fetch the result as an associative array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedules - Nurse Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content {
            padding: 20px;
        }

        .button {
            margin-right: 15px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .form-control {
            padding: 5px 10px;
            margin-top: 5px;
        }

        .btn {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Doctor Schedules</h2>

        <!-- Buttons for Navigation -->
        <div>
            <a href="patient.php" class="button">All Patients</a>
            <a href="my_patients.php" class="button">My Patients</a>
        </div>

        <!-- Doctor Schedule Table -->
        <h3>Doctor Schedule List</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Doctor ID</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Action</th>
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
                            <a href="?edit=<?= $s['DoctorScheduleID'] ?>" class="btn btn-warning">Edit</a>
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
