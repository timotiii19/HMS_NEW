<div class="sidebar">
    <h2>Cashier Dashboard</h2>
    <ul>
        <li><a href="/HMS-main/views/cashier/dashboard.php">Dashboard</a></li>
        <li><a href="/HMS-main/views/cashier/doctor.php">Doctor</a></li>
        <li><a href="/HMS-main/views/cashier/patient_billing.php">Billing</a></li>
        <li><a href="/HMS-main/views/cashier/patient.php">Patient</a></li>
        <li><a href="/HMS-main/views/cashier/pharmacy.php">Pharmacy</a></li>
        <li><a href="/HMS-main/auth/logout.php">Logout</a></li>
    </ul>
</div>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #e0f7fa;
    color: #333;
}

.sidebar {
    position: fixed;
    top: 80px; /* pushes it below the header */
    left: 0;
    width: 200px; /* your sidebar width */
    height: calc(100% - 60px); /* so it doesn’t go behind the header */
    background-color: #892a52; /* maroon */
    z-index: 1;
}

.sidebar h2 {
    color: rgb(255, 255, 255);
    text-align: center;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin: 10px 0px;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    text-align: left;
    font-size: 1em;
    padding: 10px 20px; /* consistent padding */
}

.sidebar ul li a:hover {
    background: #7a0154;
    padding-left: 30px;
    border-radius: 5px;
}

.button {
    padding: 10px 20px;
    background-color: #0288d1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.button:hover {
    background-color: #0277bd;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

table, th, td {
    border: 1px solid #b3e5fc;
}

th, td {
    padding: 10px;
    text-align: left;
}

form input, form select {
    padding: 10px;
    margin: 5px 0;
    width: 100%;
    box-sizing: border-box;
}
</style>
