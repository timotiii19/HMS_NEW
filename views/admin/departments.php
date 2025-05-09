<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');

// Connect database
$conn = new mysqli("localhost", "root", "root", "charles_hms");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Department Management</h2>

    <!-- Department Form -->
    <form method="post" action="">
        <input type="text" name="department_name" placeholder="Enter Department Name" required>
        <input type="text" name="department_room" placeholder="Enter Department Room" required>
        <button type="submit" name="add_department">Add Department</button>
    </form>

    <?php
    // Add department
    if (isset($_POST['add_department'])) {
        $dept_name = $_POST['department_name'];
        $dept_room = $_POST['department_room'];
        $stmt = $conn->prepare("INSERT INTO department (DepartmentName, DepartmentRoom) VALUES (?, ?)");
        $stmt->bind_param("ss", $dept_name, $dept_room);
        $stmt->execute();
        header("Location: departments.php");
        exit();
    }

    // Delete department
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $conn->query("DELETE FROM department WHERE DepartmentID = $id");
        header("Location: departments.php");
        exit();
    }
    ?>

    <!-- Department Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Department Name</th>
            <th>Department Room</th>
            <th>Action</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM department");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['DepartmentID']."</td>
                    <td>".$row['DepartmentName']."</td>
                    <td>".$row['DepartmentRoom']."</td>
                    <td><a href='?delete=".$row['DepartmentID']."' onclick=\"return confirm('Are you sure?')\">Delete</a></td>
                </tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
