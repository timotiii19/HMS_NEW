<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/admin_sidebar.php');
include('../../includes/db.php');

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

// Delete bill
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patientbilling WHERE BillingID=$id");
    header("Location: billing.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Billing Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
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
            $patients = $conn->query("SELECT PatientID, Name FROM patient");
            if ($patients === false) {
                echo "Error in fetching patients: " . $conn->error;
                exit();
            }
            while ($p = $patients->fetch_assoc()) {
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

        <?php
        $result = $conn->query("
            SELECT b.*, p.Name AS PatientName 
            FROM patientbilling b 
            JOIN patient p ON b.PatientID = p.PatientID
        ");
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row['BillingID']."</td>
                        <td>".$row['PatientName']."</td>
                        <td>".$row['DoctorFee']."</td>
                        <td>".$row['MedicineCost']."</td>
                        <td>".$row['TotalAmount']."</td>
                        <td>".$row['PaymentDate']."</td>
                        <td>".$row['Receipt']."</td>
                        <td><a href='?delete=".$row['BillingID']."' onclick=\"return confirm('Are you sure?')\">Delete</a></td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No billing records found.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
