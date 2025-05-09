<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'nurse') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/nurse_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>ANurse Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Welcome, Nurse!</h2>
    <p>This is your Nurse dashboard. Use the sidebar to navigate.</p>
</div>
    
</body>
</html>
