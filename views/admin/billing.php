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

// Add bill
if (isset($_POST['add_bill'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_fee = $_POST['doctor_fee'];
    $medicine_cost = $_POST['medicine_cost'];
    $payment_date = $_POST['payment_date'];
    $receipt = $_POST['receipt'];
    $total = $doctor_fee + $medicine_cost;

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO patientbilling (PatientID, DoctorFee, MedicineCost, TotalAmount, PaymentDate, Receipt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idddds", $patient_id, $doctor_fee, $medicine_cost, $total, $payment_date, $receipt);
    $stmt->execute();
    header("Location: billing.php");
    exit();
}

// Handle update bill
if (isset($_POST['update_bill'])) {
    $billing_id = $_POST['billing_id'];
    $patient_id = $_POST['patient_id'];
    $doctor_fee = $_POST['doctor_fee'];
    $medicine_cost = $_POST['medicine_cost'];
    $payment_date = $_POST['payment_date'];
    $receipt = $_POST['receipt'];
    $total = $doctor_fee + $medicine_cost;

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("UPDATE patientbilling SET PatientID=?, DoctorFee=?, MedicineCost=?, TotalAmount=?, PaymentDate=?, Receipt=? WHERE BillingID=?");
    $stmt->bind_param("iddddsd", $patient_id, $doctor_fee, $medicine_cost, $total, $payment_date, $receipt, $billing_id);
    $stmt->execute();
    header("Location: billing.php");
    exit();
}

// Delete bill
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patientbilling WHERE BillingID=$id");
    header("Location: billing.php");
    exit();
}

// Fetch bills and patients
$bills_result = $conn->query("SELECT b.*, p.Name AS PatientName FROM patientbilling b JOIN patients p ON b.PatientID = p.PatientID");
$patients_result = $conn->query("SELECT PatientID, Name FROM patients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Management</title>
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
            width: 500px;
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
    <h2>Billing Management</h2>

    <!-- Billing Form -->
    <form method="post" action="">
        <label>Patient:</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php
            while ($p = $patients_result->fetch_assoc()) {
                echo "<option value='".$p['PatientID']."'>".$p['Name']."</option>";
            }
            ?>
        </select>

        <input type="number" name="doctor_fee" placeholder="Enter Doctor Fee" step="0.01" required>
        <input type="number" name="medicine_cost" placeholder="Enter Medicine Cost" step="0.01" required>
        <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
        <input type="text" name="receipt" placeholder="Enter Receipt Number" required>
        <button type="submit" name="add_bill">Add Bill</button>
    </form>

    <!-- Billing Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient Name</th>
            <th>Doctor Fee</th>
            <th>Medicine Cost</th>
            <th>Total Amount</th>
            <th>Payment Date</th>
            <th>Receipt</th>
            <th>Action</th>
        </tr>

        <?php if ($bills_result->num_rows > 0): ?>
            <?php while ($row = $bills_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['BillingID'] ?></td>
                    <td><?= htmlspecialchars($row['PatientName']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorFee']) ?></td>
                    <td><?= htmlspecialchars($row['MedicineCost']) ?></td>
                    <td><?= htmlspecialchars($row['TotalAmount']) ?></td>
                    <td><?= htmlspecialchars($row['PaymentDate']) ?></td>
                    <td><?= htmlspecialchars($row['Receipt']) ?></td>
                    <td>
                        <button onclick="openModal(
                            <?= $row['BillingID'] ?>,
                            <?= $row['PatientID'] ?>,
                            <?= $row['DoctorFee'] ?>,
                            <?= $row['MedicineCost'] ?>,
                            '<?= $row['PaymentDate'] ?>',
                            '<?= $row['Receipt'] ?>'
                        )">Edit</button>
                        |
                        <a href="?delete=<?= $row['BillingID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No billing records found.</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Billing Details</h3>
        <form method="post" action="billing.php">
            <input type="hidden" name="billing_id" id="modal_billing_id">

            <label>Patient:</label>
            <select name="patient_id" id="modal_patient_id" required>
                <option value="">Select Patient</option>
                <?php
                // Populate patients for edit modal
                $patients_result->data_seek(0);
                while ($p = $patients_result->fetch_assoc()) {
                    echo "<option value='".$p['PatientID']."'>".$p['Name']."</option>";
                }
                ?>
            </select>

            <input type="number" name="doctor_fee" id="modal_doctor_fee" step="0.01" required>
            <input type="number" name="medicine_cost" id="modal_medicine_cost" step="0.01" required>
            <input type="date" name="payment_date" id="modal_payment_date" required>
            <input type="text" name="receipt" id="modal_receipt" required>
            <button type="submit" name="update_bill" class="save-btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal(billing_id, patient_id, doctor_fee, medicine_cost, payment_date, receipt) {
    document.getElementById('modal_billing_id').value = billing_id;
    document.getElementById('modal_patient_id').value = patient_id;
    document.getElementById('modal_doctor_fee').value = doctor_fee;
    document.getElementById('modal_medicine_cost').value = medicine_cost;
    document.getElementById('modal_payment_date').value = payment_date;
    document.getElementById('modal_receipt').value = receipt;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</body>
</html>
