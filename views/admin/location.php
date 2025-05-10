<?php
// Start session and include database config

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Handle Add
if (isset($_POST['add'])) {
    $roomType = $_POST['RoomType'];
    $roomCapacity = $_POST['RoomCapacity'];
    $availability = $_POST['Availability'];
    $building = $_POST['Building'];
    $floor = $_POST['Floor'];
    $roomNumber = $_POST['RoomNumber'];

    $sql = "INSERT INTO Location (RoomType, RoomCapacity, Availability, Building, Floor, RoomNumber) 
            VALUES ('$roomType', '$roomCapacity', '$availability', '$building', '$floor', '$roomNumber')";
    mysqli_query($conn, $sql);
    header("Location: location.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM Location WHERE LocationID = $id");
    header("Location: location.php");
    exit();
}

// Handle Edit
if (isset($_POST['update'])) {
    $id = $_POST['LocationID'];
    $roomType = $_POST['RoomType'];
    $roomCapacity = $_POST['RoomCapacity'];
    $availability = $_POST['Availability'];
    $building = $_POST['Building'];
    $floor = $_POST['Floor'];
    $roomNumber = $_POST['RoomNumber'];

    $sql = "UPDATE Location SET 
                RoomType = '$roomType',
                RoomCapacity = '$roomCapacity',
                Availability = '$availability',
                Building = '$building',
                Floor = '$floor',
                RoomNumber = '$roomNumber'
            WHERE LocationID = $id";
    mysqli_query($conn, $sql);
    header("Location: location.php");
    exit();
}

// Fetch all locations
$result = mysqli_query($conn, "SELECT * FROM Location");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Locations</title>
    <link rel="stylesheet" href="../../assets/style.css"> <!-- Optional styling -->
</head>
<body>
<h2>Manage Locations</h2>

<!-- Add Location Form -->
<form method="POST" action="location.php">
    <h3>Add Location</h3>
    <label>Room Type:</label>
    <select name="RoomType" required>
        <option value="Ward">Ward</option>
        <option value="Private">Private</option>
        <option value="Semi-Private">Semi-Private</option>
    </select><br>
    <label>Room Capacity:</label><input type="number" name="RoomCapacity" required><br>
    <label>Availability:</label>
    <select name="Availability" required>
        <option value="Occupied">Occupied</option>
        <option value="Unoccupied">Unoccupied</option>
    </select><br>
    <label>Building:</label><input type="text" name="Building" required><br>
    <label>Floor:</label><input type="number" name="Floor" required><br>
    <label>Room Number:</label><input type="number" name="RoomNumber" required><br>
    <input type="submit" name="add" value="Add Location">
</form>

<hr>

<!-- Location Table -->
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>Room Type</th><th>Capacity</th><th>Availability</th>
        <th>Building</th><th>Floor</th><th>Room Number</th><th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <form method="POST" action="location.php">
            <input type="hidden" name="LocationID" value="<?= $row['LocationID'] ?>">
            <td><?= $row['LocationID'] ?></td>
            <td>
                <select name="RoomType">
                    <option value="Ward" <?= $row['RoomType'] == 'Ward' ? 'selected' : '' ?>>Ward</option>
                    <option value="Private" <?= $row['RoomType'] == 'Private' ? 'selected' : '' ?>>Private</option>
                    <option value="Semi-Private" <?= $row['RoomType'] == 'Semi-Private' ? 'selected' : '' ?>>Semi-Private</option>
                </select>
            </td>
            <td><input type="number" name="RoomCapacity" value="<?= $row['RoomCapacity'] ?>"></td>
            <td>
                <select name="Availability">
                    <option value="Occupied" <?= $row['Availability'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                    <option value="Unoccupied" <?= $row['Availability'] == 'Unoccupied' ? 'selected' : '' ?>>Unoccupied</option>
                </select>
            </td>
            <td><input type="text" name="Building" value="<?= $row['Building'] ?>"></td>
            <td><input type="number" name="Floor" value="<?= $row['Floor'] ?>"></td>
            <td><input type="number" name="RoomNumber" value="<?= $row['RoomNumber'] ?>"></td>
            <td>
                <input type="submit" name="update" value="Update">
                <a href="location.php?delete=<?= $row['LocationID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
