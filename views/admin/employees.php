<?php
// Include the database connection file
include('../../config/db.php'); // Adjust the path based on your folder structure

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include('../../includes/admin_sidebar.php');

// Function to fetch users from the database
function getUsers($conn) {
    $query = "SELECT * FROM users";  // Changed table name from 'employees' to 'users'
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle adding a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Prevent duplicate username or email in the database
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";  // Changed table name to 'users'
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Error: Username or Email already exists!";
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";  // Changed table name to 'users'
        if (mysqli_query($conn, $query)) {
            header("Location: employees.php");  // Redirect to avoid form resubmission on refresh
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
    <title>User Management</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css"> <!-- Include your CSS file -->
</head>
<body>

<div class="content">
    <h2>User Management</h2>

    <!-- Add User Form -->
    <form method="POST" action="employees.php" class="form-container">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="doctor">Doctor</option>
            <option value="nurse">Nurse</option>
            <option value="pharmacist">Pharmacist</option>
            <option value="cashier">Cashier</option>
            <option value="receptionist">Receptionist</option>
        </select>
        <button type="submit">Add User</button>
    </form>

    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <hr>

    <!-- User Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
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
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role']; ?></td>
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
