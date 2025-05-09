<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');

// Connect to the database
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Get all locations for view-only access
$locations = $conn->query("SELECT * FROM locations");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Locations</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Locations (View-Only)</h2>

    <table border="1">
        <tr>
            <th>Location ID</th>
            <th>Building</th>
            <th>Room Number</th>
        </tr>
        <?php while ($row = $locations->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['LocationID'] ?></td>
            <td><?= $row['Building'] ?></td>
            <td><?= $row['RoomNumber'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
