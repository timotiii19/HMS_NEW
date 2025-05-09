<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');

// Connect database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Add nurse
if (isset($_POST['add_nurse'])) {
    $name = $_POST['nurse_name'];
    $email = $_POST['nurse_email'];
    $availability = $_POST['availability'];
    $contact = $_POST['contact'];
    $department_id = $_POST['department_id'];

    $stmt = $conn->prepare("INSERT INTO nurse (Name, Email, Availability, ContactNumber, DepartmentID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $email, $availability, $contact, $department_id);
    $stmt->execute();
    header("Location: nurses.php");
    exit();
}

// Delete nurse
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM nurse WHERE NurseID = $id");
    header("Location: nurses.php");
    exit();
}

// Fetch nurses
$nurses = $conn->query("SELECT n.*, d.DepartmentName FROM nurse n LEFT JOIN department d ON n.DepartmentID = d.DepartmentID");

// Fetch departments
$departments = $conn->query("SELECT * FROM department");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nurse Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Nurse Management</h2>

    <!-- Nurse Form -->
    <form method="post" action="">
        <input type="text" name="nurse_name" placeholder="Enter Nurse Name" required>
        <input type="email" name="nurse_email" placeholder="Enter Email" required>
        <input type="text" name="availability" placeholder="Enter Availability" required>
        <input type="text" name="contact" placeholder="Enter Contact Number" required>
        <select name="department_id" required>
            <option value="">-- Select Department --</option>
            <?php while ($dept = $departments->fetch_assoc()) { ?>
                <option value="<?php echo $dept['DepartmentID']; ?>"><?php echo $dept['DepartmentName']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="add_nurse">Add Nurse</button>
    </form>

    <!-- Nurse Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Availability</th>
            <th>Contact</th>
            <th>Department</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $nurses->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['NurseID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Email']; ?></td>
            <td><?php echo $row['Availability']; ?></td>
            <td><?php echo $row['ContactNumber']; ?></td>
            <td><?php echo $row['DepartmentName'] ?? 'Unassigned'; ?></td>
            <td><a href="?delete=<?php echo $row['NurseID']; ?>" onclick="return confirm('Delete this nurse?')">Delete</a></td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
