<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/cashier_sidebar.php');

// Database connection
$conn = new mysqli("localhost", "root", "root", "charles_hms");

// Fetch data
$patients = $conn->query("SELECT PatientID, Name FROM patients");
$doctors = $conn->query("SELECT DoctorID, DoctorName, DoctorFee FROM doctor");
$pharmacy = $conn->query("SELECT MedicineName, Price FROM pharmacy");

// Add billing
if (isset($_POST['add_billing'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_fee = $_POST['doctor_fee'];
    $medicine_cost = $_POST['medicine_cost'];
    $payment_date = $_POST['payment_date'];
    $receipt = $_POST['receipt'];
    $total = $doctor_fee + $medicine_cost;

    $stmt = $conn->prepare("INSERT INTO patientbilling (PatientID, DoctorFee, MedicineCost, TotalAmount, PaymentDate, Receipt) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idddds", $patient_id, $doctor_fee, $medicine_cost, $total, $payment_date, $receipt);
    $stmt->execute();
    header("Location: patientbilling.php");
    exit();
}

// Delete billing
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patientbilling WHERE BillingID=$id");
    header("Location: patientbilling.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Billing</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<div class="content">
    <h2>Patient Billing</h2>

    <form method="post">
        <label>Patient:</label>
        <select name="patient_id" required>
            <option value="">Select Patient</option>
            <?php while ($p = $patients->fetch_assoc()) {
                echo "<option value='{$p['PatientID']}'>{$p['Name']}</option>";
            } ?>
        </select>

        <label>Doctor Fee (read-only):</label>
        <select name="doctor_fee" required>
            <option value="">Select Doctor</option>
            <?php while ($d = $doctors->fetch_assoc()) {
                echo "<option value='{$d['DoctorFee']}'>{$d['DoctorName']} - ₹{$d['DoctorFee']}</option>";
            } ?>
        </select>

        <label>Medicine Cost (read-only):</label>
        <select name="medicine_cost" required>
            <option value="">Select Medicine</option>
            <?php while ($m = $pharmacy->fetch_assoc()) {
                echo "<option value='{$m['Price']}'>{$m['MedicineName']} - ₹{$m['Price']}</option>";
            } ?>
        </select>

        <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required>
        <input type="text" name="receipt" placeholder="Receipt No" required>
        <button type="submit" name="add_billing">Add Billing</button>
    </form>

    <h3>Billing Records</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor Fee</th>
            <th>Medicine Cost</th>
            <th>Total</th>
            <th>Payment Date</th>
            <th>Receipt</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("
            SELECT b.*, p.Name AS PatientName
            FROM patientbilling b
            LEFT JOIN patients p ON b.PatientID = p.PatientID
        ");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['BillingID']}</td>
                <td>{$row['PatientName']}</td>
                <td>₹{$row['DoctorFee']}</td>
                <td>₹{$row['MedicineCost']}</td>
                <td>₹{$row['TotalAmount']}</td>
                <td>{$row['PaymentDate']}</td>
                <td>{$row['Receipt']}</td>
                <td><a href='?delete={$row['BillingID']}' onclick='return confirm(\"Delete this record?\")'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
