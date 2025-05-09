<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');

// Database connection
$conn = new mysqli("localhost", "root", "root", "charles_hms");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Cashier Management</h2>

    <!-- Cashier Form -->
    <form method="post" action="">
        <input type="text" name="cashier_name" placeholder="Enter Cashier Name" required>
        <button type="submit" name="add_cashier">Add Cashier</button>
    </form>

    <?php
    // Add cashier
    if (isset($_POST['add_cashier'])) {
        $name = $_POST['cashier_name'];
        $stmt = $conn->prepare("INSERT INTO cashier (Name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: cashier.php");
        exit();
    }

    // Delete cashier
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $conn->query("DELETE FROM cashier WHERE CashierID = $id");
        header("Location: cashier.php");
        exit();
    }
    ?>

    <!-- Cashier Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Cashier Name</th>
            <th>Action</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM cashier");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['CashierID']."</td>
                    <td>".$row['Name']."</td>
                    <td><a href='?delete=".$row['CashierID']."' onclick=\"return confirm('Are you sure?')\">Delete</a></td>
                </tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
