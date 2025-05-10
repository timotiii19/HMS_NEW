<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Handle update
if (isset($_POST['update_cashier'])) {
    $cashier_id = $_POST['cashier_id'];
    $name = $_POST['name'];

    $stmt = $conn->prepare("UPDATE cashier SET Name=? WHERE CashierID=?");
    $stmt->bind_param("si", $name, $cashier_id);
    $stmt->execute();
    header("Location: cashier.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM cashier WHERE CashierID = $id");
    header("Location: cashier.php");
    exit();
}

// Fetch cashiers
$result = $conn->query("SELECT * FROM cashier");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 10px; right: 10px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Cashier Management</h2>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Cashier Name</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['CashierID'] ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td>
                        <button onclick="openModal(
                            <?= $row['CashierID'] ?>,
                            '<?= htmlspecialchars($row['Name'], ENT_QUOTES) ?>'
                        )">Edit</button>
                        |
                        <a href="?delete=<?= $row['CashierID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No cashier records found.</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Cashier Details</h3>
        <form method="post" action="cashier.php">
            <input type="hidden" name="cashier_id" id="modal_cashier_id">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="modal_name" required>
            </div>
            <button type="submit" name="update_cashier" class="save-btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(id, name) {
    document.getElementById('modal_cashier_id').value = id;
    document.getElementById('modal_name').value = name;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>
