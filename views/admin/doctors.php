<?php
session_start();
include('../../includes/db_connection.php'); // Include your DB connection

// Check if user is logged in and has admin rights
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: /HMS-main/auth/login.php');
    exit();
}

// Create a new patient or edit an existing one
$patientID = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    if ($patientID) {
        // Update patient
        $sql = "UPDATE patients SET firstName = ?, lastName = ?, dob = ?, gender = ?, contactNumber = ?, email = ?, address = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $firstName, $lastName, $dob, $gender, $contactNumber, $email, $address, $patientID);
        $stmt->execute();
    } else {
        // Create new patient
        $sql = "INSERT INTO patients (firstName, lastName, dob, gender, contactNumber, email, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $firstName, $lastName, $dob, $gender, $contactNumber, $email, $address);
        $stmt->execute();
    }

    // Redirect after the action
    header('Location: /HMS-main/views/admin/patients.php');
    exit();
}

// If editing, fetch patient details
if ($patientID) {
    $sql = "SELECT * FROM patients WHERE patientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
} else {
    $patient = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $patientID ? "Edit Patient" : "Create Patient"; ?> | Admin Dashboard</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2><?php echo $patientID ? "Edit Patient" : "Create New Patient"; ?></h2>

    <form method="POST" action="" class="mb-4">
        <div class="row g-2">
            <div class="col">
                <label for="firstName">First Name</label>
                <input type="text" name="firstName" class="form-control" placeholder="First Name" value="<?= $patient ? $patient['firstName'] : '' ?>" required>
            </div>
            <div class="col">
                <label for="lastName">Last Name</label>
                <input type="text" name="lastName" class="form-control" placeholder="Last Name" value="<?= $patient ? $patient['lastName'] : '' ?>" required>
            </div>
        </div>

        <div class="row g-2">
            <div class="col">
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?= $patient ? $patient['dob'] : '' ?>" required>
            </div>
            <div class="col">
                <label for="gender">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male" <?= $patient && $patient['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $patient && $patient['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $patient && $patient['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>

        <div class="row g-2">
            <div class="col">
                <label for="contactNumber">Contact Number</label>
                <input type="text" name="contactNumber" class="form-control" placeholder="Contact Number" value="<?= $patient ? $patient['contactNumber'] : '' ?>" required>
            </div>
            <div class="col">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" value="<?= $patient ? $patient['email'] : '' ?>" required>
            </div>
        </div>

        <div class="row g-2">
            <div class="col">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" placeholder="Address" value="<?= $patient ? $patient['address'] : '' ?>" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><?= $patientID ? "Update Patient" : "Create Patient" ?></button>
        </div>
    </form>

    <a href="/HMS-main/views/admin/patients.php" class="btn btn-secondary">Back to Patient List</a>
</div>
</body>
</html>
