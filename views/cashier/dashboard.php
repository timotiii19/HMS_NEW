<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../auth/login.php");
    exit();
}
include('../../includes/cashier_sidebar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>

<div class="content">
    <h2>Welcome, Cashier!</h2>
    <p>This is your cashier dashboard. Use the sidebar to navigate.</p>
</div>
    
</body>
</html>
