<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/cashier_sidebar.php');
include('../../config/db.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctors (for future edits if doctor role is allowed)
$doctors_result = $conn->query("SELECT DoctorID, DoctorName, DoctorFee FROM doctor");

// Fetch patients (for future edits if doctor role is allowed)
$patients_result = $conn->query("SELECT PatientID, Name FROM patients");

// Generate receipt number for adding a new bill (only accessible by doctors)
$result = $conn->query("SELECT MAX(CAST(Receipt AS UNSIGNED)) AS last_receipt FROM patientbilling");
$row = $result->fetch_assoc();
$last_receipt = $row['last_receipt'] ?? 0;
$new_receipt_number = str_pad($last_receipt + 1, 6, '0', STR_PAD_LEFT);  // Always 6 digits, padded with zeros

// Fetch all bills for display
$bills_result = $conn->query("SELECT b.*, p.Name AS PatientName, d.DoctorName
FROM patientbilling b
JOIN patients p ON b.PatientID = p.PatientID
JOIN doctor d ON b.DoctorID = d.DoctorID;");

// Check user role for permissions (admins cannot add/edit/delete)
$user_role = $_SESSION['role'] ?? '';
$can_edit = $user_role == 'doctor';  // Only doctors can add/edit/delete

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* Modal styling */
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

        /* Currency input styling */
        .currency-input {
            display: flex;
            align-items: center;
        }

        .currency-symbol {
            margin-right: 5px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>

<div class="content">
    <h2>Billing Management</h2>

    <!-- Only doctors can add new bills, admins cannot -->
    <?php if ($can_edit): ?>
        <form method="post" action="">

            <label>Patient:</label>
            <select name="patient_id" required>
                <option value="">Select Patient</option>
                <?php
                $patients_result->data_seek(0);
                while ($p = $patients_result->fetch_assoc()) {
                    echo "<option value='{$p['PatientID']}'>".htmlspecialchars($p['Name'])."</option>";
                }
                ?>
            </select>

            <label>Doctor:</label>
            <select name="doctor_id" id="doctorSelect" onchange="updateDoctorFee()" required>
                <option value="">Select Doctor</option>
                <?php
                $doctors_result->data_seek(0);
                while ($d = $doctors_result->fetch_assoc()) {
                    echo "<option value='{$d['DoctorID']}' data-fee='{$d['DoctorFee']}'>".htmlspecialchars($d['DoctorName'])."</option>";
                }
                ?>
            </select>

            <label>Doctor Fee:</label>
            <div class="currency-input">
                <span class="currency-symbol">₱</span>
                <input type="number" name="doctor_fee" id="doctorFee" step="0.01" readonly required>
            </div>

            <input type="number" name="medicine_cost" placeholder="Enter Medicine Cost" step="0.01" required>
            <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label>Receipt Number:</label>
            <input type="text" name="receipt" id="receipt" value="<?php echo $new_receipt_number; ?>" readonly>

            <button type="submit" name="add_bill">Add Bill</button>
        </form>
    <?php else: ?>
        <!-- Admins can't add bills -->
        <p>You do not have permission to add bills.</p>
    <?php endif; ?>

    <!-- Table with all bills -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Doctor Fee</th>
            <th>Medicine Cost</th>
            <th>Total Amount</th>
            <th>Payment Date</th>
            <th>Receipt</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $bills_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['BillingID'] ?></td>
                <td><?= htmlspecialchars($row['PatientName']) ?></td>
                <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                <td><?= htmlspecialchars($row['DoctorFee']) ?></td>
                <td><?= htmlspecialchars($row['MedicineCost']) ?></td>
                <td><?= htmlspecialchars($row['TotalAmount']) ?></td>
                <td><?= htmlspecialchars($row['PaymentDate']) ?></td>
                <td><?= htmlspecialchars($row['Receipt']) ?></td>
                <td>
                    <?php if ($can_edit): ?>
                        <button onclick="openModal(
                            <?= $row['BillingID'] ?>,
                            <?= $row['PatientID'] ?>,
                            <?= $row['DoctorID'] ?>,
                            <?= $row['DoctorFee'] ?>,
                            <?= $row['MedicineCost'] ?>,
                            '<?= $row['PaymentDate'] ?>',
                            '<?= $row['Receipt'] ?>'
                        )">Edit</button> |
                        <a href="?delete=<?= $row['BillingID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php else: ?>
                        <span>No action allowed</span>  <!-- Admin cannot edit or delete -->
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Modal for editing bills -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h3>Edit Billing</h3>
        <form method="post" action="billing.php">
            <input type="hidden" name="billing_id" id="modal_billing_id">

            <label>Patient:</label>
            <select name="patient_id" id="modal_patient_id" required>
                <option value="">Select Patient</option>
                <?php
                $patients_result->data_seek(0);
                while ($p = $patients_result->fetch_assoc()) {
                    echo "<option value='{$p['PatientID']}'>".htmlspecialchars($p['Name'])."</option>";
                }
                ?>
            </select>

            <label>Doctor:</label>
            <select name="doctor_id" id="modal_doctor_id" onchange="updateModalDoctorFee()" required>
                <option value="">Select Doctor</option>
                <?php
                $doctors_result->data_seek(0);
                while ($d = $doctors_result->fetch_assoc()) {
                    echo "<option value='{$d['DoctorID']}' data-fee='{$d['DoctorFee']}'>".htmlspecialchars($d['DoctorName'])."</option>";
                }
                ?>
            </select>

            <label>Doctor Fee:</label>
            <div class="currency-input">
                <span class="currency-symbol">₱</span>
                <input type="number" name="doctor_fee" id="modal_doctor_fee" step="0.01" readonly required>
            </div>

            <input type="number" name="medicine_cost" id="modal_medicine_cost" step="0.01" required>
            <input type="date" name="payment_date" id="modal_payment_date" required>
            <input type="text" name="receipt" id="modal_receipt" required>
            <button type="submit" name="update_bill">Save</button>
        </form>
    </div>
</div>

<script>
function updateDoctorFee() {
    const select = document.getElementById('doctorSelect');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('doctorFee').value = fee || '';
    // Display peso sign before fee
    document.getElementById('doctorFee').value = fee ? '₱' + fee : '₱0.00';
}

function updateModalDoctorFee() {
    const select = document.getElementById('modal_doctor_id');
    const fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('modal_doctor_fee').value = fee || '';
    // Display peso sign before fee
    document.getElementById('modal_doctor_fee').value = fee ? '₱' + fee : '₱0.00';
}

function openModal(billing_id, patient_id, doctor_id, doctor_fee, medicine_cost, payment_date, receipt) {
    document.getElementById('modal_billing_id').value = billing_id;
    document.getElementById('modal_patient_id').value = patient_id;
    document.getElementById('modal_doctor_id').value = doctor_id;
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
