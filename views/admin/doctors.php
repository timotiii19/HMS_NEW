<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle update (edit doctor details)
if (isset($_POST['update_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    $availability = $_POST['availability'];
    $contact = $_POST['contact'];
    $doctor_type = $_POST['doctor_type'];
    $department_id = $_POST['department_id'];
    $location_id = $_POST['location_id'];
    $room_type = $_POST['room_type'];
    $doctor_fee = $_POST['doctor_fee'];

    $stmt = $conn->prepare("UPDATE doctor SET Availability=?, ContactNumber=?, DoctorType=?, DepartmentID=?, LocationID=?, RoomType=?, DoctorFee=? WHERE DoctorID=?");
    $stmt->bind_param("sssissdi", $availability, $contact, $doctor_type, $department_id, $location_id, $room_type, $doctor_fee, $doctor_id);
    $stmt->execute();
    header("Location: doctors.php");
    exit();
}

// Handle delete doctor + user
if (isset($_GET['delete'])) {
    $doctor_id = $_GET['delete'];

    // Get UserID first
    $result = $conn->query("SELECT UserID FROM doctor WHERE DoctorID = $doctor_id");
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['UserID'];

        // Delete from doctors table
        $conn->query("DELETE FROM doctor WHERE DoctorID = $doctor_id");

        // Delete from users table
        $conn->query("DELETE FROM users WHERE id = $user_id");
    }

    header("Location: doctors.php");
    exit();
}

// Fetch doctors with user info
$result = $conn->query("
    SELECT d.DoctorID, u.username AS DoctorName, u.email AS Email, d.Availability, d.ContactNumber, d.DoctorType, d.DepartmentID, d.LocationID, d.RoomType, d.DoctorFee 
    FROM doctor d 
    JOIN users u ON d.UserID = u.id
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .edit-modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 800px;
            max-width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .modal-close {
            color: #fff;
            font-size: 30px;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .modal-close:hover {
            color: #f44336;
        }

        h3 {
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        .save-btn {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .save-btn:hover {
            background-color: #45a049;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .content {
            padding: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Two Column Layout */
        .form-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-column {
            width: 48%;
        }

    </style>
    <script>
        function showEditForm(id) {
            document.getElementById('edit-modal-' + id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById('edit-modal-' + id).style.display = 'none';
        }
    </script>
</head>
<body>
<div class="content">
    <h2>Doctor Management</h2>

    <table border="1">
        <tr>
            <th>DoctorID</th>
            <th>DoctorName</th>
            <th>Email</th>
            <th>Availability</th>
            <th>ContactNumber</th>
            <th>DoctorType</th>
            <th>DepartmentID</th>
            <th>LocationID</th>
            <th>RoomType</th>
            <th>DoctorFee</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['DoctorID'] ?></td>
                    <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Availability']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorType']) ?></td>
                    <td><?= htmlspecialchars($row['DepartmentID']) ?></td>
                    <td><?= htmlspecialchars($row['LocationID']) ?></td>
                    <td><?= htmlspecialchars($row['RoomType']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorFee']) ?></td>
                    <td>
                        <button onclick="showEditForm(<?= $row['DoctorID'] ?>)">Edit</button> |
                        <a href="?delete=<?= $row['DoctorID'] ?>" onclick="return confirm('Are you sure you want to delete this doctor and user?')">Delete</a>
                    </td>
                </tr>
                <tr id="edit-modal-<?= $row['DoctorID'] ?>" class="edit-modal">
                    <td colspan="11">
                        <div class="edit-modal-content">
                            <span class="modal-close" onclick="closeModal(<?= $row['DoctorID'] ?>)">×</span>
                            <h3>Edit Doctor Details</h3>
                            <form method="post" action="doctors.php">
                                <input type="hidden" name="doctor_id" value="<?= $row['DoctorID'] ?>">

                                <div class="form-row">
                                    <div class="form-column">
                                        <div class="form-group">
                                            <label for="availability">Availability</label>
                                            <input type="text" name="availability" placeholder="Availability" value="<?= htmlspecialchars($row['Availability']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="doctor_type">Doctor Type</label>
                                            <input type="text" name="doctor_type" placeholder="Doctor Type" value="<?= htmlspecialchars($row['DoctorType']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="department_id">Department ID</label>
                                            <input type="text" name="department_id" placeholder="Department ID" value="<?= htmlspecialchars($row['DepartmentID']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="room_type">Room Type</label>
                                            <input type="text" name="room_type" placeholder="Room Type" value="<?= htmlspecialchars($row['RoomType']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-column">
                                        <div class="form-group">
                                            <label for="contact">Contact Number</label>
                                            <input type="text" name="contact" placeholder="Contact Number" value="<?= htmlspecialchars($row['ContactNumber']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="location_id">Location ID</label>
                                            <input type="text" name="location_id" placeholder="Location ID" value="<?= htmlspecialchars($row['LocationID']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($row['Email']) ?>" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="doctor_fee">Doctor Fee</label>
                                            <input type="number" step="0.01" name="doctor_fee" placeholder="Doctor Fee" value="<?= htmlspecialchars($row['DoctorFee']) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="update_doctor" class="save-btn">Save Changes</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="11">No doctor records found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
