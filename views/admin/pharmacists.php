<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');

// Connect database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Add pharmacist
if (isset($_POST['add_pharmacist'])) {
    $name = $_POST['pharmacist_name'];
    $contact = $_POST['pharmacist_contact'];

    $stmt = $conn->prepare("INSERT INTO pharmacist (Name, ContactNumber) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $contact);
    $stmt->execute();
    header("Location: pharmacists.php");
    exit();
}

// Delete pharmacist
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pharmacist WHERE PharmacistID = $id");
    header("Location: pharmacists.php");
    exit();
}

// Display pharmacists
$result = $conn->query("SELECT * FROM pharmacist");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pharmacist Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Pharmacist Management</h2>

    <!-- Pharmacist Form -->
    <form method="post" action="">
        <input type="text" name="pharmacist_name" placeholder="Enter Pharmacist Name" required>
        <input type="text" name="pharmacist_contact" placeholder="Enter Contact Number" required>
        <button type="submit" name="add_pharmacist">Add Pharmacist</button>
    </form>

    <!-- Pharmacist Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Pharmacist Name</th>
            <th>Contact Number</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['PharmacistID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['ContactNumber']; ?></td>
            <td><a href="?delete=<?php echo $row['PharmacistID']; ?>" onclick="return confirm('Delete this pharmacist?')">Delete</a></td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
