<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');
include('../../config/db.php');

// Function to fetch users from the database
function getUsers($conn) {
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle adding a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Error: Username or Email already exists!";
    } else {
        $query = "INSERT INTO users (username, full_name, email, password, role) 
                  VALUES ('$username', '$full_name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            // Get the last inserted user ID
            $last_user_id = mysqli_insert_id($conn);

            // Insert data based on the user's role
            if ($role == 'doctor') {
                $doctor_query = "INSERT INTO doctor (DoctorID, UserID, DoctorName, Email, Availability, ContactNumber, DoctorType, DepartmentID, LocationID, RoomType, DoctorFee) 
                                 VALUES ('$last_user_id', '$last_user_id', '$full_name', '$email', '', '', '', NULL, NULL, '', NULL)"; // Insert name and email here
                mysqli_query($conn, $doctor_query);
            } elseif ($role == 'nurse') {
                $nurse_query = "INSERT INTO nurse (Name, Email, Availability, ContactNumber, DepartmentID) 
                                 VALUES ('$full_name', '$email', '', '', NULL)";
                mysqli_query($conn, $nurse_query);
            } elseif ($role == 'pharmacist') {
                $stmt = $conn->prepare("INSERT INTO pharmacist (Name, Email, ContactNumber) VALUES (?, ?, ?)");
                $empty_contact = '';
                $stmt->bind_param("sss", $full_name, $email, $empty_contact);
                $stmt->execute();
            } elseif ($role == 'cashier') {
                $cashier_query = "INSERT INTO cashier (Name) 
                                  VALUES ('$full_name')";
                mysqli_query($conn, $cashier_query);
            }

            // Refresh the page to show the updated user list after adding a new user
            header("Location: employees.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}


$users = getUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <style>
        .content { padding: 20px; }
        .form-container { margin-bottom: 20px; }
        .form-container input, .form-container select, .form-container button {
            margin: 5px 0; padding: 8px; width: 100%;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        tr:hover { background-color: #f5f5f5; }
    </style>
</head>
<body>

<div class="content">
    <h2>Employee Management</h2>

    <!-- Add User Form -->
    <div class="form-container">
        <form method="POST" action="employees.php">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="text" name="full_name" placeholder="Enter Full Name" required>  <!-- Added full name field -->
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
                <option value="pharmacist">Pharmacist</option>
                <option value="cashier">Cashier</option>
                <option value="receptionist">Receptionist</option>
            </select>
            <button type="submit">Add User</button>
        </form>

        <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
    </div>

    <!-- User Table -->
    <h3>User List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>  <!-- Display Full Name -->
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>  <!-- Show Full Name -->
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
