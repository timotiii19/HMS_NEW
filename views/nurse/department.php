<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');

// Connect to database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Get all departments for view-only access
$departments = $conn->query("SELECT * FROM department");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Departments</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Departments (View-Only)</h2>

    <table border="1">
        <tr>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>Department Room</th>
        </tr>
        <?php while ($row = $departments->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['DepartmentID'] ?></td>
            <td><?= $row['DepartmentName'] ?></td>
            <td><?= $row['DepartmentRoom'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
