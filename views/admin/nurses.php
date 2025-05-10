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

// Handle update (edit nurse details)
if (isset($_POST['update_nurse'])) {
    $nurse_id = $_POST['nurse_id'];
    $availability = $_POST['availability'];
    $contact = $_POST['contact'];
    $department_id = $_POST['department_id'];

    $stmt = $conn->prepare("UPDATE nurses SET Availability=?, ContactNumber=?, DepartmentID=? WHERE NurseID=?");
    $stmt->bind_param("ssii", $availability, $contact, $department_id, $nurse_id);
    $stmt->execute();
    header("Location: nurses.php");
    exit();
}

// Handle delete nurse
if (isset($_GET['delete'])) {
    $nurse_id = $_GET['delete'];

    // Delete from nurses table
    $conn->query("DELETE FROM nurses WHERE NurseID = $nurse_id");

    header("Location: nurses.php");
    exit();
}

// Fetch nurses with user info
$result = $conn->query("
    SELECT n.NurseID, u.username AS NurseName, u.email AS Email, n.Availability, n.ContactNumber, n.DepartmentID
    FROM nurse n 
    JOIN users u ON n.UserID = u.id
");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display nurse details
    }
} else {
    echo "No nurse records found.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Management</title>
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
    <h2>Nurse Management</h2>

            <table border="1">
            <tr>
                <th>NurseID</th>
                <th>Name</th>
                <th>DepartmentID</th>
                <th>Email</th>
                <th>Availability</th>
                <th>ContactNumber</th>
                <th>Action</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['NurseID'] ?></td>
                        <td><?= htmlspecialchars($row['NurseName']) ?></td>
                        <td><?= htmlspecialchars($row['DepartmentID']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td><?= htmlspecialchars($row['Availability']) ?></td>
                        <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                        <td>
                            <button onclick="showEditForm(<?= $row['NurseID'] ?>)">Edit</button> |
                            <a href="?delete=<?= $row['NurseID'] ?>" onclick="return confirm('Are you sure you want to delete this nurse?')">Delete</a>
                        </td>
                    </tr>
                    <tr id="edit-modal-<?= $row['NurseID'] ?>" class="edit-modal">
                        <td colspan="7">
                            <div class="edit-modal-content">
                                <span class="modal-close" onclick="closeModal(<?= $row['NurseID'] ?>)">×</span>
                                <h3>Edit Nurse Details</h3>
                                <form method="post" action="nurses.php">
                                    <input type="hidden" name="nurse_id" value="<?= $row['NurseID'] ?>">

                                    <div class="form-row">
                                        <div class="form-column">
                                            <div class="form-group">
                                                <label for="availability">Availability</label>
                                                <input type="text" name="availability" placeholder="Availability" value="<?= htmlspecialchars($row['Availability']) ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="department_id">Department ID</label>
                                                <input type="text" name="department_id" placeholder="Department ID" value="<?= htmlspecialchars($row['DepartmentID']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-column">
                                            <div class="form-group">
                                                <label for="contact">Contact Number</label>
                                                <input type="text" name="contact" placeholder="Contact Number" value="<?= htmlspecialchars($row['ContactNumber']) ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($row['Email']) ?>" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="update_nurse" class="save-btn">Save Changes</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No nurse records found.</td></tr>
            <?php endif; ?>
        </table>

</div>
</body>
</html>
